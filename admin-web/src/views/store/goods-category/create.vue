<template>
    <a-modal :title="operation == 'create' ? '添加分类' : '编辑分类'" @BeforeOk="onCreateOk" @BeforeCancel="close" :width="isMobile?'calc(100% - 20px)':'400px'"
        :top="useSetting().ModalTop" class="modal" v-model:visible="visible" :align-center="false" title-align="start"
        render-to-body>
        <a-form layout="vertical" :model="createForm" ref="createRef" :rules="createRules" v-loading="initLoading">
            <a-form-item :label-col-flex="labelColFlex" label="父级分类" field="pid">
                <a-select v-model="createForm.pid" placeholder="请选择父级分类" allow-clear>
                    <a-option v-for="item in parentTree" :key="item.value" :label="item.label" :value="item.value"
                        :disabled="item.disabled" />
                </a-select>
            </a-form-item>
            <a-form-item :label-col-flex="labelColFlex" label="分类名称" field="category_name">
                <a-input v-model="createForm.category_name" placeholder="请输入分类名称"></a-input>
            </a-form-item>
            <a-form-item :label-col-flex="labelColFlex" label="分类图标" field="icon">
                <upload-btn v-model="createForm.icon" width="60px" height="60px" count="1"></upload-btn>
            </a-form-item>
            <a-form-item :label-col-flex="labelColFlex" label="分类大图" field="image">
                <upload-btn v-model="createForm.image" width="100%" height="170px" count="1"></upload-btn>
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

<script lang="ts" >
export default {
    name: "createGoodsCateComponent",
};
</script>
<script lang="ts" setup>
import { ref, reactive, getCurrentInstance, nextTick } from "vue";
import { Result, ResultError, EnumItemType } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import { getStoreGoodsCategoryDetailApi, getStoreGoodsCategorySelectTreeApi, createStoreGoodsCategoryApi, updateStoreGoodsCategoryApi } from "@/api/store/goods-category";
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
    pid: null,
    category_name: "",
    sort: 0,
    status: 1,
    icon: "",
    image: "",
});

const createRules: any = reactive({
    category_name: [{ required: true, message: "分类名称不能为空！" }],
    icon: [{ required: true, message: "分类图标不能为空！" }],
    image: [{ required: true, message: "分类大图不能为空！" }],
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
                operationApi = createStoreGoodsCategoryApi(createForm.value);
            } else {
                operationApi = updateStoreGoodsCategoryApi(
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

const parentTree = ref<any>([]);

const toInit = () => {
    initCascader();
    if (!operationId.value) {
        return;
    }
    initLoading.value = true;
    getStoreGoodsCategoryDetailApi({ id: operationId.value })
        .then((res: Result) => {
            createForm.value.category_name = res.data.category_name;
            createForm.value.pid = res.data.pid;
            createForm.value.sort = res.data.sort;
            createForm.value.icon = res.data.icon;
            createForm.value.image = res.data.image;
            createForm.value.status = res.data.status.value;

            setTimeout(() => {
                initLoading.value = false;
            }, 500);
        })
        .catch((err: ResultError) => {
            close();
            initLoading.value = false;
            $utils.errorMsg(err);
        });
};

const initCascader = () => {
    let obj: any = {};
    if (operationId.value) {
        obj["pid"] = operationId.value;
    }
    getStoreGoodsCategorySelectTreeApi(obj)
        .then((res: Result) => {
            parentTree.value = res.data;
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