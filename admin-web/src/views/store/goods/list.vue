<template>
    <layout-body-content pageHeader>
        <template v-slot:page-header-left>
            商品管理
        </template>
        <template v-slot:page-header-right>
            <a-space>
                <a-tooltip content="刷新">
                    <a-button @click="toInit()" size="small"><icon-refresh /></a-button>
                </a-tooltip>
                <a-button v-permission="'store-goods-create'" size="small" type="primary" @click="onCreate(0)">
                    新增商品
                </a-button>
            </a-space>
        </template>
        <template v-slot:content="{
            height
        }">
        </template>
    </layout-body-content>
</template>
<script lang="ts" setup>
import { getStoreGoodsListApi } from "@/api/store/goods";
import { useSetting } from "@/hooks/useSetting";
import router from "@/router";
import { EnumItemType, PageLimitType, Result, ResultError } from "@/types";
import { ref, getCurrentInstance, onMounted, watch } from "vue";




const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;


const initLoading = ref<boolean>(false);

const listPage = ref<any>({
    total: 0,
    limit: useSetting().PageLimit.value,
    page: 1,
});

const pageChange = (res: PageLimitType) => {
    listPage.value = res;
    toInit();
};

const lists = ref<any>([]);

// 初始化
const toInit = (initPage: boolean = false) => {
    if (initPage) {
        listPage.value.page = 1;
    }
    let obj: any = {};
    obj.page = listPage.value.page;
    obj.limit = listPage.value.limit;

    initLoading.value = true;
    getStoreGoodsListApi(obj)
        .then((res: Result) => {
            lists.value = res.data.data;
            listPage.value.total = res.data.total;
            initLoading.value = false;
        })
        .catch((err: ResultError) => {
            initLoading.value = false;
            $utils.errorMsg(err);
        });
};


const onCreate = (id: number | string = 0) => {
    router.push({
        path: "/store/goods/create",
        query: id ? { id } : {},
    });
};

</script>