<template>
    <a-drawer class="drawer-default"  :title="operation == 'create' ? '添加规格模板' : '编辑规格模板'" v-model:visible="visible" :width="isMobile?'calc(100% - 20px)':'800px'">
        <a-form v-loading="initLoading" layout="vertical" :model="createForm" ref="createRef" :rules="createRules">
            <a-form-item :label-col-flex="labelColFlex" label="规格模板名称" field="spec_temp_name">
                <a-input v-model="createForm.spec_temp_name" placeholder="请输入规格模板名称"></a-input>
            </a-form-item>
            <a-form-item :label-col-flex="labelColFlex" label="规格属性" field="attrs">
                <div class="flex items-center">
                    <a-input placeholder="请输入规格名" v-model="specName">
                    </a-input>
                    <a-button class="ml10" type="primary" @click="onCreateSpecName">添加规格</a-button>
                </div>
            </a-form-item>
            <a-form-item :label-col-flex="labelColFlex">
                <storeGoodsSpecValue v-if="visible" height="360px" v-model="createForm.attrs" ref="storeGoodsSpecValueRef">
                </storeGoodsSpecValue>
            </a-form-item>
        </a-form>
        <template #footer>
            <a-space>
                <a-button v-btn @click="close">取消</a-button>
                <a-button v-btn type="primary" :disabled="initLoading || btnLoading" :loading="btnLoading"
                    @click="onCreateOk">确定</a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script lang="ts" >
export default {
    name: "createGoodsServerComponent",
};
</script>
<script lang="ts" setup>
import { ref, reactive, getCurrentInstance, nextTick } from "vue";
import { EnumItemType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import { getStoreGoodsSpecTemplatesDetailApi, createStoreGoodsSpecTemplatesApi, updateStoreGoodsSpecTemplatesApi } from "@/api/store/goods-spec-templates";
import { useAppStore, useEnumStore } from "@/store";
import storeGoodsSpecValue from "./goods-spec-value.vue";
import { storeToRefs } from "pinia";

const { isMobile } = storeToRefs(useAppStore());

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const labelColFlex = ref<string>("80px");

const operation = ref<string>("create");

const operationId = ref<number>(0);

const visible = ref<boolean>(false);

const createRef = ref<HTMLElement>();

const createForm = ref<any>({
    spec_temp_name: "",
    attrs: [],
});

const createRules: any = reactive({
    spec_temp_name: [{ required: true, message: "规格模板名称不能为空！" }],
});

const initLoading = ref<boolean>(false);

const emit = defineEmits(["success"]);

const specName = ref<string>("");

const onCreateSpecName = () => {
    if (specName.value == "") {
        $utils.errorMsg("请输入规格名");
        return false;
    }
    if (specName.value.indexOf(" ") !== -1) {
        $utils.errorMsg("规格名不能包含空格");
        return false;
    }
    createForm.value.attrs.push({
        name: specName.value,
        values: [],
    });
    specName.value = "";
};

const btnLoading = ref(false);

const onCreateOk = () => {
    proxy?.$refs['createRef']?.validate((valid: any, fields: any) => {
        if (!valid) {
            if (btnLoading.value) {
                return;
            }
            if (createForm.value.attrs.length == 0) {
                $utils.errorMsg("请至少添加一条商品规格！");
                return false;
            }
            var isError = false;
            createForm.value.attrs.forEach(
                (item: { values: string[]; name: string }) => {
                    if (item.values.length == 0) {
                        $utils.errorMsg("规格规格值不能为空！");
                        isError = true;
                    }
                }
            );
            if (isError || btnLoading.value) {
                return false;
            }
            btnLoading.value = true;
            let operationApi: any = null;
            if (operation.value == "create") {
                operationApi = createStoreGoodsSpecTemplatesApi(createForm.value);
            } else {
                operationApi = updateStoreGoodsSpecTemplatesApi(
                    Object.assign(
                        {
                            id: operationId.value,
                        },
                        createForm.value
                    )
                );
            }
            operationApi
                .then((res: Result) => {
                    $utils.successMsg(res.message);
                    close();
                    emit("success");
                    btnLoading.value = false;
                })
                .catch((err: ResultError) => {
                    $utils.errorMsg(err);
                    btnLoading.value = false;
                });
        }
    });
};

const toInit = () => {
    if (!operationId.value) {
        return;
    }
    initLoading.value = true;
    getStoreGoodsSpecTemplatesDetailApi({ id: operationId.value })
        .then((res: Result) => {
            createForm.value.spec_temp_name = res.data.spec_temp_name;
            createForm.value.attrs = res.data.attrs;
            proxy?.$refs["storeGoodsSpecValueRef"].refresh(res.data.attrs);
            setTimeout(() => {
                initLoading.value = false;
            }, 500);
        })
        .catch((err: ResultError) => {
            initLoading.value = false;
            $utils.errorMsg(err);
        });
};

const selectModalIconRef = ref<any>();

const iconChange = (icon: string) => {
    createForm.value.icon = icon;
};

const open = (id: number = 0) => {
    if (id != 0) {
        operation.value = "update";
        operationId.value = id;
    } else {
        operation.value = "create";
    }
    nextTick(() => {
        toInit();
    });
    visible.value = true;
};

const close = () => {
    proxy?.$refs['createRef']?.resetFields();
    createForm.value.attrs = [];
    operationId.value = 0;
    visible.value = false;
    return true;
};

defineExpose({ open });
</script>