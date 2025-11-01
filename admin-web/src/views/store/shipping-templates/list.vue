<template>
    <layout-body-content pageHeader>
        <template v-slot:page-header-left>
            运费模板管理
        </template>
        <template v-slot:page-header-right>
            <a-space>
                <a-tooltip content="刷新">
                    <a-button @click="toInit()" size="small"><icon-refresh /></a-button>
                </a-tooltip>
                <a-button v-permission="'store-shipping-templates-create'" size="small" type="primary"
                    @click="onCreate(0)">
                    添加
                </a-button>
                <createShippingTemplatesComponent ref="createShippingTemplatesComponentRef" @success="toInit(true)">
                </createShippingTemplatesComponent>
            </a-space>
        </template>
        <template v-slot:content="{
            height
        }">
            <a-table :loading="initLoading" :data="lists" row-key="id" isLeaf :pagination="false" :scroll="{
                x: '100%',
                y: height - 39
            }" :row-selection="{
                type: 'checkbox',
                showCheckedAll: true,
                onlyCurrent: false,
            }" v-model:selectedKeys="selectedIds" :expandable="{
                width: 80
            }">
                <template #expand-row="{ record }">
                    <div class="templates-citys-box">
                        <div class="templates-citys-item" v-if="record.citys.length > 0">
                            <div>配送区域:</div>
                            <template v-for="(province, index) in $utils.getRegionsTreeData(
                                record.citys[0],
                                regionList
                            )" :key="index">
                                <span class="ml2 mb2 fw">{{ province.name }}</span>
                                <template v-if="!province.isAllCitys && province.citys.length > 0">
                                    [<template v-for="(city, idx) in province.citys" :key="idx">
                                        <span class="text-grey">{{ city.name }}</span>
                                        <span v-if="idx + 1 < province.citys.length">、</span>
                                    </template>]
                                </template>
                                ;
                            </template>
                        </div>
                        <div class="templates-citys-item" v-if="record.free_citys.length > 0">
                            <div>
                                包邮区域：
                            </div>
                                <template v-for="(province, index) in $utils.getRegionsTreeData(
                                    record.free_citys[0],
                                    regionList
                                )" :key="index">
                                    <span class="ml2 mb2 fw">{{ province.name }}
                                    </span>
                                    <template v-if="!province.isAllCitys && province.citys.length > 0">
                                        [<template v-for="(city, idx) in province.citys" :key="idx">
                                            <span class="text-grey">{{ city.name }}</span>
                                            <span v-if="idx + 1 < province.citys.length">、</span>
                                        </template>]
                                    </template>
                                    ;
                                </template>
                        </div>
                    </div>
                </template>
                <template #columns>
                    <a-table-column title="名称" data-index="name" :width="240">
                        <template #cell="{ record }">
                            <div>{{ record.name }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="计费方式" data-index="type" :width="200">
                        <template #cell="{ record }">
                            <span v-if="record.type.name">{{ record.type.name }}</span>
                        </template>
                    </a-table-column>
                    <a-table-column title="是否指定区域包邮" data-index="is_free_shipping" align="center" :width="160">
                        <template #cell="{ record }">
                            <a-tag color="red" v-if="record.is_free_shipping === 1">是</a-tag>
                            <a-tag v-else>否</a-tag>
                        </template>
                    </a-table-column>
                    <a-table-column title="创建时间" data-index="create_time" align="center" :width="160">
                        <template #cell="{ record }">
                            <div class="text-grey">{{ record.create_time }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="操作" align="center" :width="140">
                        <template #cell="{ record }">
                            <a-space>
                                <a-button @click=" onCreate(record.id)" v-permission="'store-shipping-templates-update'"
                                    size="small">编辑</a-button>
                                <div v-permission="'store-shipping-templates-delete'">
                                    <a-popconfirm content="确定删除吗？" @ok="onDelete(record.id)">
                                        <template #icon>
                                            <icon-exclamation-circle-fill type="red" />
                                        </template>
                                        <a-button size="small">删除</a-button>
                                    </a-popconfirm>
                                </div>
                            </a-space>
                        </template>
                    </a-table-column>
                </template>
            </a-table>
        </template>
        <template #footer>
            <page :listPage="listPage" @change="pageChange"></page>
        </template>
    </layout-body-content>
</template>
<script lang="ts" setup>
import { ref, getCurrentInstance, onMounted, nextTick } from "vue";

import createShippingTemplatesComponent from './create.vue';
import { useSetting } from "@/hooks/useSetting";
import { PageLimitType, RegionsItemType, Result, ResultError } from "@/types";
import { deleteStoreShippingTemplatesApi, getStoreShippingTemplatesListApi } from "@/api/store/shipping-templates";
import { storeToRefs } from "pinia";
import { useRegionsStore } from "@/store";

const { regions } = storeToRefs(useRegionsStore());

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const createShippingTemplatesComponentRef = ref<HTMLElement>()

const onCreate = (id: number) => {
    proxy?.$refs['createShippingTemplatesComponentRef'].open(id)
}

const initLoading = ref<boolean>(false);

const lists = ref([]);

const toInit = (isInit: boolean = false) => {
    if (isInit) {
        listPage.value.page = 1;
    }
    initLoading.value = true;
    let obj: any = {};
    obj.page = listPage.value.page;
    obj.limit = listPage.value.limit;
    getStoreShippingTemplatesListApi(obj)
        .then((res: Result) => {
            lists.value = res.data.data;
            listPage.value.total = res.data.total;


            setTimeout(() => {
                initLoading.value = false;
            }, 300);
        })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
            initLoading.value = false;
        });
};


const checkCitys = (citys: any) => {
    citys.forEach((item: any) => {
        item.treeData = $utils.getRegionsTreeData(
            item,
            regionList.value
        );
    });
    return citys;
};

const listPage = ref<any>({
    total: 0,
    limit: useSetting().PageLimit.value,
    page: 1,
});

const pageChange = (res: PageLimitType) => {
    listPage.value = res;
    toInit();
};

const onDelete = (id: number) => {
    deleteStoreShippingTemplatesApi({
        id,
    })
        .then((res: Result) => {
            toInit();
            $utils.successMsg(res.message);
        })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
        });
};

const regionList = ref<RegionsItemType[]>([]);

onMounted(() => {
    toInit();
    regionList.value = regions.value?.tree;
});

const selectedIds = ref<string[] | number[]>([])
</script>


<style scoped>
.templates-citys-item{
    margin-bottom: 20px;
}
</style>