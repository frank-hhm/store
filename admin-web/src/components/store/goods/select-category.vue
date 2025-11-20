<template>
    <div class="store-goods-select-box">
        <div class="store-goods-select">
            <a-cascader v-model="_modelValue" :options="cateData" allow-search allow-clear check-strictly
                placeholder="请选择商品分类" multiple :field-names="{
                    label: 'label',
                    value: 'value',
                    children: 'children'
                }" :max-tag-count="1" @popup-visible-change="visibleChange" />
        </div>
        <div class="ml20" v-permission="'store-goods-category-create'">
            <a-button type="text" @click="onCreateLabel">新增分类</a-button>
        </div>

        <createGoodsCategoryCreate ref="createGoodsCategoryCreateRef" @success="toInit()">
        </createGoodsCategoryCreate>
    </div>
</template>
<script lang="ts">
export default {
    name: "storeGoodsSelectCategory",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, watch, onMounted, nextTick } from "vue";
import { getStoreGoodsCategorySelectTreeApi } from "@/api/store/goods-category";
import { useSetting } from "@/hooks/useSetting";
import { PageLimitType, Result, ResultError } from "@/types";
import createGoodsCategoryCreate from "@/views/store/goods-category/create.vue";

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

const cateData = ref<any[]>([])

const visibleChange = (res: any) => {
    if (res === true) {
        toInit();
    }
}

const initLoading = ref<boolean>(false)

const toInit = () => {
    initLoading.value = true;
    getStoreGoodsCategorySelectTreeApi({})
        .then((res: Result) => {
            cateData.value = res.data
            initLoading.value = false;
        })
        .catch((err: ResultError) => {
            initLoading.value = false;
            $utils.errorMsg(err);
        });
};

const createGoodsCategoryCreateRef = ref<HTMLElement>();

const onCreateLabel = () => {
    proxy?.$refs["createGoodsCategoryCreateRef"]?.open();
};

onMounted(() => {
    nextTick(() => {
        emit("update:modelValue", props.modelValue);
    })
})

watch(
    () => props.modelValue,
    () => {
        _modelValue.value = props.modelValue;
        emit("update:modelValue", _modelValue.value);
    }
)
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