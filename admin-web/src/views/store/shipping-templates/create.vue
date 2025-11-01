<template>
    <a-drawer class="drawer-default" :title="operation == 'create' ? '添加运费模版' : '编辑运费模版'" v-model:visible="visible"
        :width="isMobile ? 'calc(100% - 20px)' : '1000px'">

        <div v-if="visible">
            <a-form v-loading="initLoading || initRegionLoading" :model="createForm" ref="createRef"
                :rules="createRules">
                <a-row :gutter="20">
                    <a-col :md="24" :xs="24">
                        <a-row>
                            <a-col :md="12" :xs="24">
                                <a-form-item :label-col-flex="labelColFlex" label="模板名称" field="name">
                                    <a-input v-model="createForm.name" placeholder="请输入模板名称"></a-input>
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-col>
                    <a-col :md="24" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="计费方式" field="type">
                            <a-radio-group v-model="createForm.type">
                                <template v-for="(item, index) in typeEnum" :key="index">
                                    <a-radio :value="item.value">{{ item.name }}</a-radio>
                                </template>
                            </a-radio-group>
                        </a-form-item>
                    </a-col>
                    <a-col :md="24" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="配送区域及运费" field="citys">
                            <div style="width: 100%">
                                <a-table :data="createForm.citys" :pagination="false">
                                    <template #columns>
                                        <a-table-column title="可包邮区域" data-index="free_shipping_citys">
                                            <template #cell="{ record }">
                                                <template v-for="(province, index) in record.treeData" :key="index">
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
                                            </template>
                                        </a-table-column>
                                        <a-table-column :title="'首件' + (typeEnum[createForm.type].unit || '')"
                                            data-index="citys" :width="120">
                                            <template #cell="{ record, rowIndex }">
                                                <a-form-item row-class="mb0" hide-asterisk hide-label
                                                    :field="`citys[${rowIndex}].first`"
                                                    :rules="{ required: true, message: '请选择输入' }">
                                                    <a-input-number v-model="createForm.citys[rowIndex].first"
                                                        size="mini">
                                                    </a-input-number>
                                                </a-form-item>
                                            </template>
                                        </a-table-column>

                                        <a-table-column title="运费 (元)" :width="120" data-index="citys">
                                            <template #cell="{ record, rowIndex }">
                                                <a-form-item row-class="mb0" hide-asterisk hide-label
                                                    :field="`citys[${rowIndex}].first_money`"
                                                    :rules="{ required: true, message: '请选择输入' }">
                                                    <a-input-number :precision="2"
                                                        v-model="createForm.citys[rowIndex].first_money" size="mini">
                                                    </a-input-number>
                                                </a-form-item>
                                            </template>
                                        </a-table-column>

                                        <a-table-column data-index="citys"
                                            :title="'续件' + (typeEnum[createForm.type].unit || '')" :width="120">
                                            <template #cell="{ record, rowIndex }">
                                                <a-form-item row-class="mb0" hide-asterisk hide-label
                                                    :field="`citys[${rowIndex}].add_number`"
                                                    :rules="{ required: true, message: '请选择输入' }">
                                                    <a-input-number v-model="createForm.citys[rowIndex].add_number"
                                                        size="mini">
                                                    </a-input-number>
                                                </a-form-item>
                                            </template>
                                        </a-table-column>
                                        <a-table-column data-index="citys" title="续费（元）" :width="120">
                                            <template #cell="{ record, rowIndex }">
                                                <a-form-item row-class="mb0" hide-asterisk hide-label
                                                    :field="`citys[${rowIndex}].add_money`"
                                                    :rules="{ required: true, message: '请选择输入' }">
                                                    <a-input-number v-model="createForm.citys[rowIndex].add_money"
                                                        size="mini">
                                                    </a-input-number>
                                                </a-form-item>
                                            </template>
                                        </a-table-column>

                                        <a-table-column title="操作" :width="120">
                                            <template #cell="{ record, rowIndex }">
                                                <a-space>
                                                    <a-button size="mini" type="text"
                                                        @click="onEditSelectRegion('citys', record, rowIndex)">编辑</a-button>
                                                    <a-popconfirm ok-text="确定" cancel-text="不用了" content="确定删除吗？"
                                                        @ok="onDeleteSelectRegion('citys', rowIndex)">
                                                        <a-button size="mini" type="text">删除</a-button>
                                                    </a-popconfirm>
                                                </a-space>
                                            </template>
                                        </a-table-column>
                                    </template>
                                    <template #empty>
                                        <div class="text-center text-grey">
                                            暂无数据
                                        </div>
                                    </template>
                                </a-table>
                                <a-button class="mt10" size="mini" type="text"
                                    @click="onAddRegion('citys')">添加可配送区域和运费</a-button>
                            </div>
                        </a-form-item>
                    </a-col>
                    <a-col :md="24" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="指定包邮" field="is_free_shipping">
                            <a-radio-group v-model="createForm.is_free_shipping">
                                <a-radio :value="1">开启</a-radio>
                                <a-radio :value="0">关闭</a-radio>
                            </a-radio-group>
                        </a-form-item>
                    </a-col>
                    <a-col :md="24" :xs="24" v-if="createForm.is_free_shipping == 1">
                        <a-form-item :label-col-flex="labelColFlex" field="free_shipping_citys">
                            <div style="width: 100%;">
                                <a-table style="width: 100%" :data="createForm.free_shipping_citys" :pagination="false">
                                    <template #columns>
                                        <a-table-column data-index="free_shipping_citys" title="可包邮区域">
                                            <template #cell="{ record, rowIndex }">

                                                <template v-for="(province, index) in record.treeData" :key="index">
                                                    <span class="ml2 mb2 fw" v-if="province">{{
                                                        province.name }}
                                                    </span>
                                                    <template v-if="!province.isAllCitys && province.citys.length > 0">
                                                        [<template v-for="(city, idx) in province.citys" :key="idx">
                                                            <span class="text-grey" v-if="city">{{ city.name
                                                            }}</span>
                                                            <span v-if="idx + 1 < province.citys.length">、</span>
                                                        </template>]
                                                    </template>
                                                    ;
                                                </template>
                                            </template>
                                        </a-table-column>
                                        <a-table-column data-index="free_shipping_citys" :title="'包邮' +
                                            (createForm.type == 1
                                                ? '件数'
                                                : (typeEnum[createForm.type].unit || ''))
                                            " :width="120">
                                            <template #cell="{ record, rowIndex }">
                                                <a-form-item row-class="mb0" hide-asterisk hide-label
                                                    :field="`free_shipping_citys[${rowIndex}].number`"
                                                    :rules="{ required: true, message: '请选择输入' }">
                                                    <a-input-number v-model="createForm.free_shipping_citys[rowIndex].number
                                                        " size="mini">
                                                    </a-input-number>
                                                </a-form-item>
                                            </template>
                                        </a-table-column>
                                        <a-table-column data-index="free_shipping_citys" title="满额包邮（元）" :width="120">
                                            <template #cell="{ record, rowIndex }">
                                                <a-form-item row-class="mb0" hide-asterisk hide-label
                                                    :field="`free_shipping_citys[${rowIndex}].money`"
                                                    :rules="{ required: true, message: '请选择输入' }">
                                                    <a-input-number :precision="2" v-model="createForm.free_shipping_citys[rowIndex].money
                                                        " size="small">
                                                    </a-input-number>
                                                </a-form-item>
                                            </template>
                                        </a-table-column>
                                        <a-table-column title="操作" :width="120">
                                            <template #cell="{ record, rowIndex }">
                                                <a-space>
                                                    <a-button size="mini" type="text" @click="
                                                        onEditSelectRegion(
                                                            'free_shipping_citys',
                                                            record,
                                                            rowIndex
                                                        )
                                                        ">编辑</a-button>
                                                    <a-popconfirm ok-text="确定" cancel-text="不用了" content="确定删除吗？" @ok="
                                                        onDeleteSelectRegion('free_shipping_citys', rowIndex)
                                                        ">
                                                        <a-button size="mini" type="text">删除</a-button>
                                                    </a-popconfirm>
                                                </a-space>
                                            </template>
                                        </a-table-column>
                                    </template>
                                    <template #empty>
                                        <div class="text-center text-grey">
                                            暂无数据
                                        </div>
                                    </template>
                                </a-table>
                                <a-button class="mt10" size="mini" type="text"
                                    @click="onAddRegion('free_shipping_citys')">添加可包邮区域</a-button>
                            </div>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="排序" field="sort">
                            <a-input-number v-model="createForm.sort" :min="0" :max="10000" />
                        </a-form-item>
                    </a-col>
                </a-row>
            </a-form>
        </div>
        <selectRegion ref="selectRegionModalRef" v-if="visible" :lists="regionList" @change="selectRegionChange">
        </selectRegion>
        <template #footer>
            <a-space>
                <a-button v-btn @click="close">取消</a-button>
                <a-button v-btn type="primary" :disabled="initRegionLoading || initLoading || btnLoading"
                    :loading="btnLoading" @click="onCreateOk">确定</a-button>
            </a-space>
        </template>
    </a-drawer>
</template>


<script lang="ts">
export default {
    name: "createShippingTemplatesComponent",
};
</script>
<script lang="ts" setup>
import { ref, reactive, getCurrentInstance, nextTick } from "vue";
import { EnumItemType, RegionsItemType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import { getStoreShippingTemplatesDetailApi, createStoreShippingTemplatesApi, updateStoreShippingTemplatesApi } from "@/api/store/shipping-templates";
import { useAppStore, useEnumStore, useRegionsStore } from "@/store";
import selectRegion from "./select-region.vue";
import { getTreeListApi } from "@/api/region";
import { storeToRefs } from "pinia";

const { isMobile } = storeToRefs(useAppStore());
const { regions } = storeToRefs(useRegionsStore());

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["success"]);

const labelColFlex = ref<string>("100px");

const typeEnum = ref<EnumItemType[]>([]);

const operation = ref<string>("create");

const operationId = ref<number>(0);

const visible = ref<boolean>(false);

const createRef = ref<HTMLElement>();

const createForm = ref<any>({
    name: "",
    type: 1,
    citys: [],
    is_free_shipping: 0,
    free_shipping_citys: [],
    sort: 0,
});

const createRules: any = reactive({
    name: [{ required: true, message: "运费模版名称不能为空！" }],
    citys: [
        {
            type: "array",
            required: true,
            message: "请选择配送区域",
            min: 1,
        },
    ],
});

const btnLoading = ref<boolean>(false);

const onCreateOk = () => {
    proxy?.$refs['createRef']?.validate((valid: any, fields: any) => {
        if (!valid) {
            if (btnLoading.value) {
                return;
            }
            btnLoading.value = true;
            let operationApi: any = null;
            if (operation.value == "create") {
                operationApi = createStoreShippingTemplatesApi(createForm.value);
            } else {
                operationApi = updateStoreShippingTemplatesApi(
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
    })
}

const initLoading = ref<boolean>(false);

const toInit = () => {
    if (!operationId.value) {
        initLoading.value = false;
        return;
    }
    initLoading.value = true;
    nextTick(() => {
        getStoreShippingTemplatesDetailApi({ id: operationId.value })
            .then((res: Result) => {
                createForm.value.name = res.data.name;
                createForm.value.type = res.data.type.value;
                createForm.value.sort = res.data.sort;
                createForm.value.is_free_shipping = res.data.is_free_shipping;
                createForm.value.citys = checkCitys(res.data.citys);
                createForm.value.free_shipping_citys = checkCitys(res.data.free_citys);
                setTimeout(() => {
                    initLoading.value = false;
                }, 300);
            })
            .catch((err: ResultError) => {
                $utils.errorMsg(err);
            });
    });
}

const checkCitys = (citys: any) => {
    citys.forEach((item: any) => {
        item.treeData = $utils.getRegionsTreeData(
            item,
            regionList.value
        );
    });
    return citys;
};

const initRegionLoading = ref<boolean>(false)

const toInitRegion = () => {
    regionList.value = regions.value?.tree;
    cityCount.value = regions.value.count.city;
    visible.value = true;
    toInit();
    // console.log(treeData)
    // initRegionLoading.value = true
    // getTreeListApi()
    //     .then((res: Result) => {
    //         visible.value = true;
    //         regionList.value = res.data.tree;
    //         cityCount.value = res.data.count.city;
    //         initRegionLoading.value = false
    //         toInit();
    //     })
    //     .catch((err: ResultError) => {
    //         initRegionLoading.value = false
    //         $utils.errorMsg(err)
    //     });
};

const open = (id: number = 0) => {
    if (id != 0) {
        operation.value = "update";
        operationId.value = id;
    } else {
        operation.value = "create";
    }
    nextTick(() => {
        typeEnum.value = useEnumStore().getEnumItem("store.ShippingTypeEnum");
        toInitRegion();
    });
};

const close = () => {
    proxy?.$refs['createRef']?.resetFields();
    createForm.value.citys = []
    operationId.value = 0;
    visible.value = false;
    return true;
};


const selectRegionModalRef = ref<HTMLElement>();

const regionList = ref<RegionsItemType[]>([]);

const cityCount = ref<number>(0);

const onAddRegion = (field: string) => {
    // 判断是否选择了全国
    var fieldCheck = createForm.value[field] || [],
        total = 0;
    fieldCheck.forEach((item: any) => {
        total += item.citys.length;
    });
    if (total != 0 && total >= cityCount.value) {
        $utils.errorMsg("已经选择了所有区域~");
        return false;
    }
    proxy?.$refs["selectRegionModalRef"]?.open(field, fieldCheck, -1);
}

const selectRegionChange = (res: any) => {
    var regionField = res.field;
    if (res.editIndex != -1) {
        createForm.value[regionField][res.editIndex].province = res.province;
        createForm.value[regionField][res.editIndex].citys = res.citys;
        createForm.value[regionField][res.editIndex].treeData = $utils.getRegionsTreeData(res, regionList.value);
        return false;
    }
    var formItem = (
        regionField == "region"
            ? {
                province: res.province,
                citys: res.citys,
                treeData: $utils.getRegionsTreeData(
                    res,
                    regionList.value
                ),
                first: "",
                first_fee: "",
                add_number: "",
                add_money: "",
            }
            : {
                province: res.province,
                citys: res.citys,
                treeData: $utils.getRegionsTreeData(
                    res,
                    regionList.value
                ),
                money: "",
                number: "",
            });
    createForm.value[regionField].push(formItem);
};

const onEditSelectRegion = (field: string, item: any, index: number) => {
    let fieldCheck = createForm.value[field] || [];
    proxy?.$refs["selectRegionModalRef"]?.open(field, fieldCheck, index);
};

const onDeleteSelectRegion = (field: string, index: number) => {
    createForm.value[field].splice(index, 1);
};


defineExpose({ open });
</script>