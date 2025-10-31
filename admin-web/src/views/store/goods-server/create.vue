<template>
    <a-modal :title="operation == 'create' ? '添加服务' : '编辑服务'" @BeforeOk="onCreateOk" @BeforeCancel="close"  :width="isMobile?'calc(100% - 20px)':'400px'"
        :top="useSetting().ModalTop" class="modal" v-model:visible="visible" :align-center="false" title-align="start"
        render-to-body>
        <a-form v-loading="initLoading" layout="vertical" :model="createForm" ref="createRef" :rules="createRules">
            <a-form-item :label-col-flex="labelColFlex" label="服务名称" field="server_name">
                <a-input v-model="createForm.server_name" placeholder="请输入服务名称"></a-input>
            </a-form-item>
            <a-form-item :label-col-flex="labelColFlex" label="描述" field="desc">
                <a-textarea v-model="createForm.desc" placeholder="请输入内容描述" allow-clear />
            </a-form-item>
            <a-form-item :label-col-flex="labelColFlex" :label="'图标'" field="icon" class="default-append">
                <a-input v-model="createForm.icon" placeholder="选择图标">
                    <template #prefix>
                        <icon-user />
                    </template>
                    <template #append>
                        <a-button @click="() => {
                            selectModalIconRef.open();
                        }
                        ">
                            图标
                        </a-button>
                        <select-icon-modal ref="selectModalIconRef" @change="iconChange"></select-icon-modal>
                    </template>
                </a-input>
            </a-form-item>
            <a-form-item :label-col-flex="labelColFlex" label="排序" field="sort">
                <a-input-number v-model="createForm.sort" :min="0" :max="10000" />
            </a-form-item>
            <a-form-item :label-col-flex="labelColFlex" label="状态" field="status">
                <a-radio-group v-model="createForm.status">
                    <a-radio v-for="item in StatusEnum" :value="item.value" :key="item.value">{{ item.name
                        }}</a-radio>
                </a-radio-group>
            </a-form-item>
        </a-form>
        <template #footer>
            <a-space>
                <a-button v-btn @click="close">取消</a-button>
                <a-button v-btn type="primary" :disabled="initLoading || btnLoading" :loading="btnLoading"
                    @click="onCreateOk">确定</a-button>
            </a-space>
        </template>
    </a-modal>
</template>

<script lang="ts">
export default {
    name: "createGoodsServerComponent",
};
</script>
<script lang="ts" setup>
import { ref, reactive, getCurrentInstance, nextTick } from "vue";
import { EnumItemType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import { getStoreGoodsServerDetailApi, createStoreGoodsServerApi, updateStoreGoodsServerApi } from "@/api/store/goods-server";
import { useAppStore, useEnumStore } from "@/store";
import { storeToRefs } from "pinia";

const { isMobile } = storeToRefs(useAppStore());

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const labelColFlex = ref<string>("80px");

const StatusEnum = ref<EnumItemType[]>(useEnumStore().getEnumItem("StatusEnum"));

const operation = ref<string>("create");

const operationId = ref<number>(0);

const visible = ref<boolean>(false);

const createRef = ref<HTMLElement>();

const createForm = ref<any>({
    server_name: "",
    sort: 0,
    status: 1,
    icon: "",
    desc: ''
});

const createRules: any = reactive({
    server_name: [{ required: true, message: "服务名称不能为空！" }],
    desc: [{ required: true, message: "服务内容描述！" }],
    icon: [{ required: true, message: "图标不能为空！" }],
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
                operationApi = createStoreGoodsServerApi(createForm.value);
            } else {
                operationApi = updateStoreGoodsServerApi(
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
    getStoreGoodsServerDetailApi({ id: operationId.value })
        .then((res: Result) => {
            createForm.value.server_name = res.data.server_name;
            createForm.value.sort = res.data.sort;
            createForm.value.icon = res.data.icon;
            createForm.value.desc = res.data.desc;
            createForm.value.status = res.data.status.value || 1;
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
    operationId.value = 0;
    visible.value = false;
    return true;
};

defineExpose({ open });
</script>