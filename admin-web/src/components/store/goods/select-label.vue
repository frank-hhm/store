<template>
    <div class="store-goods-select-box">
        <div class="store-goods-select">
            <a-select v-model="_modelValue" multiple placeholder="请选择商品标签" :max-tag-count="1" v-loading="initLoading"
                @dropdown-reach-bottom="onReachBottom" @popup-visible-change="visibleChange" @search="handleSearch"
                allow-clear>
                <a-option v-for="item in labelData" :key="item.id" :label="item.label_name" :value="item.id" />
            </a-select>
        </div>
        <div class="ml20" v-permission="'store-goods_label-create'">
            <a-button type="text" @click="onCreateLabel">新增标签</a-button>
        </div>

        <!-- 添加商品标签组件 -->
        <createGoodsLabelCreate ref="createGoodsLabelCreateRef" @success="toInit(true)"></createGoodsLabelCreate>
    </div>
</template>
<script lang="ts">
export default {
    name: "storeGoodsSelectLabel",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, watch } from "vue";
import { getStoreGoodsLabelListApi } from "@/api/store/goods-label";
import { useSetting } from "@/hooks/useSetting";
import { PageLimitType, Result, ResultError } from "@/types";
import createGoodsLabelCreate from "@/views/store/goods-label/create.vue";


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


const labelData = ref<any[]>([])

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
    obj.label_name = searchValue.value;
    // if()
    initLoading.value = true;
    getStoreGoodsLabelListApi(obj)
        .then((res: Result) => {
            if (isInit) {
                labelData.value = res.data.data
            } else {
                labelData.value = [...labelData.value, ...res.data.data]
            }

            listPage.value.total = res.data.total;
            initLoading.value = false;
        })
        .catch((err: ResultError) => {
            initLoading.value = false;
            $utils.errorMsg(err);
        });
};


const createGoodsLabelCreateRef = ref<HTMLElement>();

const onCreateLabel = () => {
    proxy?.$refs["createGoodsLabelCreateRef"]?.open();
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