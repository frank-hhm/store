<template>
    <div class="store-goods-select-box">
        <div class="store-goods-select">
            <a-select v-model="_modelValue" multiple placeholder="请选择商品服务" :max-tag-count="1" v-loading="initLoading"
                @dropdown-reach-bottom="onReachBottom" @popup-visible-change="visibleChange" @search="handleSearch"
                allow-clear>
                <a-option v-for="item in serverData" :key="item.id" :label="item.server_name" :value="item.id" />
            </a-select>
        </div>
        <div class="ml20" v-permission="'store-goods_label-create'">
            <a-button type="text" @click="onCreateServer">新增服务</a-button>
        </div>

        <!-- 添加商品服务组件 -->
        <createGoodsServerCreate ref="createGoodsServerCreateRef" @success="toInit(true)"></createGoodsServerCreate>
    </div>
</template>
<script lang="ts">
export default {
    name: "storeGoodsSelectServer",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, watch } from "vue";
import { getStoreGoodsServerListApi } from "@/api/store/goods-server";
import { useSetting } from "@/hooks/useSetting";
import { PageLimitType, Result, ResultError } from "@/types";
import createGoodsServerCreate from "@/views/store/goods-server/create.vue";


const {
    proxy,
    proxy: { $message, $utils },
} = getCurrentInstance() as any;
const props = withDefaults(
    defineProps<{
        modelValue?: any[];
        height?: string;
    }>(),
    {
        modelValue: () => {
            return [];
        },
    }
);

const emit = defineEmits(["update:modelValue"]);

const _modelValue = ref<any>(props.modelValue);


const serverData = ref<any[]>([])

const onReachBottom = () => {
    console.log(111)
    listPage.value.page++;
    toInit()
}

const visibleChange = (res: any) => {
    if (res === true) {
        toInit(true);
    }
    console.log(res)
}

const listPage = ref<any>({
    total: 0,
    limit: useSetting().PageLimit.value,
    page: 1,
});

const pageChange = (res: PageLimitType) => {
    listPage.value = res;
    toInit();
};

const searchValue = ref<string>("")

const handleSearch = (value: string) => {
    searchValue.value = value
    toInit(true)
}

const initLoading = ref<boolean>(false)

const toInit = (isInit: boolean = false) => {
    if (isInit) {
        listPage.value.page = 1;
    }
    let obj: any = {};
    obj.page = listPage.value.page;
    obj.limit = listPage.value.limit;
    obj.server_name = searchValue.value;
    // if()
    initLoading.value = true;
    getStoreGoodsServerListApi(obj)
        .then((res: Result) => {
            if (isInit) {
                serverData.value = res.data.data
            } else {
                serverData.value = [...serverData.value, ...res.data.data]
            }

            listPage.value.total = res.data.total;
            initLoading.value = false;
        })
        .catch((err: ResultError) => {
            initLoading.value = false;
            $utils.errorMsg(err);
        });
};


const createGoodsServerCreateRef = ref<HTMLElement>();

const onCreateServer = () => {
    proxy?.$refs["createGoodsServerCreateRef"]?.open();
};

watch(
    () => _modelValue.value,
    (val) => {
        emit("update:modelValue", val);
    },
    { deep: true }
);
</script>


<style scoped>
.store-goods-select-box {
    width: 100%;
    display: flex;
    align-items: center;
}

.store-goods-select {
    width: calc(100% - 120px);
}
</style>