<template>
    <div class="store-goods-select-box">
        <div class="store-goods-select">
            <a-select placeholder="请选择规格" v-model="_modelValue" @popup-visible-change="visibleChange"
                @search="handleSearch" :max-tag-count="1" @dropdown-reach-bottom="onReachBottom">
                <a-option v-for="item in specTemplatesData" :key="item.id" :label="item.spec_temp_name"
                    :value="item.id">{{ item.spec_temp_name }}</a-option>
            </a-select>
            <div class="ml20" v-permission="'store-goods-spec-templates-create'">
                <a-button type="text" @click="onCreate">新增规格</a-button>
            </div>
        </div>
        <div class="mt10">
            <a-button type="outline" @click="onSelectedTemplates">
                确定模版
            </a-button>
        </div>
        <createGoodsSpecTemplatesCreate ref="createGoodsSpecTemplatesCreateRef" @success="toInit(true)">
        </createGoodsSpecTemplatesCreate>
    </div>
</template>
<script lang="ts">
export default {
    name: "storeGoodsSelectSpecTemplates",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, watch, nextTick, onMounted } from "vue";
import { getStoreGoodsSpecTemplatesListApi } from "@/api/store/goods-spec-templates";
import { useSetting } from "@/hooks/useSetting";
import { PageLimitType, Result, ResultError } from "@/types";
import createGoodsSpecTemplatesCreate from "@/views/store/goods-spec-templates/create.vue";


const {
    proxy,
    proxy: { $message, $utils },
} = getCurrentInstance() as any;
const props = withDefaults(
    defineProps<{
        modelValue?: number | undefined | null;
        height?: string;
    }>(),
    {
        modelValue: null,
    }
);

const emit = defineEmits(["update:modelValue", "change"]);

const _modelValue = ref<any>(props.modelValue);


const specTemplatesData = ref<any[]>([])

const onReachBottom = () => {
    listPage.value.page++;
    toInit()
}

const visibleChange = (res: any) => {
    if (res === true) {
        toInit(true);
    }
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
    getStoreGoodsSpecTemplatesListApi(obj)
        .then((res: Result) => {
            if (isInit) {
                specTemplatesData.value = res.data.data
            } else {
                specTemplatesData.value = [...specTemplatesData.value, ...res.data.data]
            }

            listPage.value.total = res.data.total;
            initLoading.value = false;
        })
        .catch((err: ResultError) => {
            initLoading.value = false;
            $utils.errorMsg(err);
        });
};


const createGoodsSpecTemplatesCreateRef = ref<HTMLElement>();

const onCreate = () => {
    proxy?.$refs["createGoodsSpecTemplatesCreateRef"]?.open();
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

const onSelectedTemplates = () => {
    emit("change", $utils.getArrayItemByKeyValue(
        specTemplatesData.value,
        "id",
        _modelValue.value
    ));
}
</script>


<style scoped>
.store-goods-select-box {
    width: 100%;
}

.store-goods-select {
    width: calc(100% - 120px);
    display: flex;
    align-items: center;
}
</style>