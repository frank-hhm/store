<template>
    <a-modal :title="operation == 'create' ? '添加单位' : '编辑单位'" @BeforeOk="onCreateOk" @BeforeCancel="close"  :width="isMobile?'calc(100% - 20px)':'400px'"
        :top="useSetting().ModalTop" class="modal" v-model:visible="visible" :align-center="false" title-align="start"
        render-to-body>
        <a-form :model="createForm" ref="createRef" :rules="createRules" v-loading="initLoading">
            <a-form-item :label-col-flex="labelColFlex" label="" field="unit_name">
                <a-input v-model="createForm.unit_name" placeholder="请输入单位名称"></a-input>
            </a-form-item>
        </a-form>
        <template #footer>
            <a-space>
                <a-button v-btn @click="close">取消</a-button>
                <a-button v-btn type="primary" :disabled="initLoading || btnLoading" :loading="btnLoading" @click="onCreateOk">确定</a-button>
            </a-space>
        </template>
    </a-modal>
</template>

<script lang="ts" >
export default {
    name: "createGoodsUnitComponent",
};
</script>
<script lang="ts" setup>
import { ref, reactive, getCurrentInstance, nextTick } from "vue";
import { Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import { getStoreGoodsUnitDetailApi, createStoreGoodsUnitApi, updateStoreGoodsUnitApi } from "@/api/store/goods-unit";
import { storeToRefs } from "pinia";
import { useAppStore } from "@/store";

const { isMobile } = storeToRefs(useAppStore());

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const labelColFlex = ref<string>("0");

const operation = ref<string>("create");

const operationId = ref<number>(0);

const visible = ref<boolean>(false);

const createRef = ref<HTMLElement>();

const createForm = ref<any>({
    unit_name: "",
});

const createRules: any = reactive({
    unit_name: [{ required: true, message: "单位名称不能为空！" }],
});

const initLoading = ref<boolean>(false);

const emit = defineEmits(["success"]);

const btnLoading = ref(false);

const onCreateOk = () => {
    proxy?.$refs['createRef']?.validate((valid: any, fields: any) => {
        if (!valid) {
            if (btnLoading.value) {
                return;
            }
            btnLoading.value = true;
            let operationApi: any = null;
            if (operation.value == "create") {
                operationApi = createStoreGoodsUnitApi(createForm.value);
            } else {
                operationApi = updateStoreGoodsUnitApi(
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
    getStoreGoodsUnitDetailApi({ id: operationId.value })
        .then((res: Result) => {
            createForm.value.unit_name = res.data.unit_name;
            setTimeout(() => {
                initLoading.value = false;
            }, 500);
        })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
        });
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
    operationId.value = 0;
    visible.value = false;
    return true;
};

defineExpose({ open });
</script>