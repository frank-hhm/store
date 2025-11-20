<template>
    <div class="store-goods-select-box">
        <div class="store-goods-select">
            <a-select v-model="_modelValue" placeholder="请选择商品单位" :max-tag-count="1" v-loading="initLoading"
                @dropdown-reach-bottom="onReachBottom" @popup-visible-change="visibleChange" @search="handleSearch"
                allow-clear>
                <a-option v-for="item in unitData" :key="item.id" :label="item.unit_name" :value="item.id" />
            </a-select>
        </div>
        <div class="ml20" v-permission="'store-goods_label-create'">
            <a-button type="text" @click="onCreateLabel">新增单位</a-button>
        </div>

        <!-- 添加商品标签组件 -->
        <createGoodsUnitCreate ref="createGoodsUnitCreateRef" @success="toInit(true)"></createGoodsUnitCreate>
    </div>
</template>
<script lang="ts">
export default {
    name: "storeGoodsSelectUnit",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, watch } from "vue";
import { getStoreGoodsUnitListApi } from "@/api/store/goods-unit";
import { useSetting } from "@/hooks/useSetting";
import { PageLimitType, Result, ResultError } from "@/types";
import createGoodsUnitCreate from "@/views/store/goods-unit/create.vue";


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


const unitData = ref<any[]>([])

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
    obj.unit_name = searchValue.value;
    // if()
    initLoading.value = true;
    getStoreGoodsUnitListApi(obj)
        .then((res: Result) => {
            if (isInit) {
                unitData.value = res.data.data
            } else {
                unitData.value = [...unitData.value, ...res.data.data]
            }

            listPage.value.total = res.data.total;
            initLoading.value = false;
        })
        .catch((err: ResultError) => {
            initLoading.value = false;
            $utils.errorMsg(err);
        });
};


const createGoodsUnitCreateRef = ref<HTMLElement>();

const onCreateLabel = () => {
    proxy?.$refs["createGoodsUnitCreateRef"]?.open();
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