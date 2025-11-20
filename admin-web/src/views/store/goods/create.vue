<template>
  <layout-body-content pageHeader hideFooter>
    <template v-slot:page-header-left>
      <a-radio-group type="button" v-model="tabIndex" @change="tabChange">
        <a-radio v-for="(item, index) in tabList" :key="index" :disabled="initLoading" :value="item.value">{{
          item.name }}</a-radio>
      </a-radio-group>
    </template>
    <template v-slot:content="{
      height
    }">
      <a-form :model="createForm" ref="createRef" :rules="createRules" v-loading="initLoading">
        <div class="form-main" v-show="tabIndex === 0">
          <div class="form-main-item">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="商品名称" field="goods_name" validate-trigger="blur">
                <a-input v-model="createForm.goods_name" placeholder="请输入商品名称"></a-input>
              </a-form-item>
            </div>
          </div>
          <div class="form-main-item">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="商品分类" field="category_id" validate-trigger="blur">
                <componentStoreGoodsSelectCategory v-model="createForm.category_id"></componentStoreGoodsSelectCategory>
              </a-form-item>
            </div>
          </div>
          <a-form-item :label-col-flex="labelColFlex" label="商品图" field="goods_images">
            <upload-btn v-model="createForm.goods_images" width="80px" height="80px" count="9"></upload-btn>
            <template #extra>
              建议尺寸：800 * 800px，可拖拽改变图片顺序，默认首张图为主图，最多上传9张
            </template>
          </a-form-item>
          <div class="form-main-item mt10">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="商品标签" field="goods_label" validate-trigger="blur">
                <componentStoreGoodsSelectLabel v-model="createForm.goods_label"></componentStoreGoodsSelectLabel>
              </a-form-item>
            </div>
          </div>
          <div class="form-main-item">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="单位" field="goods_unit" validate-trigger="blur">
                <componentStoreGoodsSelectUnit v-model="createForm.goods_unit"></componentStoreGoodsSelectUnit>
              </a-form-item>
            </div>
          </div>
          <div class="form-main-item">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="商品卖点" field="selling_point">
                <a-textarea v-model="createForm.selling_point" placeholder="请输入商品卖点"></a-textarea>
              </a-form-item>
            </div>
          </div>
        </div>
        <div class="form-main" v-show="tabIndex === 1">
          <div class="form-main-item">
            <a-form-item :label-col-flex="labelColFlex" label="商品规格" field="spec_type" validate-trigger="blur">
              <a-radio-group v-model="createForm.spec_type">
                <template v-for="(item, index) in specTypeEnum" :key="index">
                  <a-radio :value="item.value">{{ item.name }}</a-radio>
                </template>
              </a-radio-group>
            </a-form-item>
          </div>

          <template v-if="createForm.spec_type == 1">
            <template v-for="(item, index) in specForm" :key="index">
              <div class="form-main-item">
                <a-form-item :label-col-flex="labelColFlex" v-if="item.type !== 'image'" :label="`${item.name}：`"
                  :field="`spec[${0}].${item.key}`" :rules="item.required
                    ? [
                      {
                        required: true,
                        message: `请输入${item.name}`,
                      },
                    ]
                    : []
                    " validate-trigger="blur">
                  <a-input v-if="item.input_type" v-model="createForm.spec[0][item.key]"
                    :placeholder="`请输入${item.name}`">
                    <template v-if="item.unit" #suffix>
                      {{ item.unit }}
                    </template>
                  </a-input>
                  <a-input-number v-else :precision="item.is_money ? 2 : 0" v-model="createForm.spec[0][item.key]"
                    :placeholder="`请输入${item.name}`">
                    <template v-if="item.is_money" #prefix>
                      ¥
                    </template>
                    <template v-if="item.unit" #suffix>
                      {{ item.unit }}
                    </template>
                  </a-input-number>
                </a-form-item>
              </div>
            </template>
          </template>
          <template v-else>
            <div class="form-main-item">
              <a-form-item :label-col-flex="labelColFlex" label="规格模版">
                <componentStoreGoodsSelectSpecTemplates v-model="specTemplatesId"
                  @change="onChangeStoreGoodsSelectSpecTemplates">
                </componentStoreGoodsSelectSpecTemplates>
                <template #extra>
                  可选择已保存的对应模版进行生成
                </template>
              </a-form-item>
            </div>
            <div class="mt10">
              <a-form-item :label-col-flex="labelColFlex" label="">
                <storeGoodsSpec v-model="specData" ref="storeGoodsSpecRef"></storeGoodsSpec>
              </a-form-item>
            </div>
            <div class="mt10">
              <a-form-item :label-col-flex="labelColFlex" label="">
                <div class="flex">
                  <a-input placeholder="请输入规格名" v-model="specName"></a-input>
                  <a-button class="ml10" type="primary" bg text @click="onCreateSpecName()">添加新规格</a-button>
                  <a-button class="ml10" type="primary" @click="onSaveSpec">生成规格属性</a-button>
                </div>
                <template #extra>
                  注意：生成规格属性12小时内有效，过期需重新生成
                </template>
              </a-form-item>
            </div>
            <!-- 批量设置 -->
            <div class="mt10" v-if="createForm.spec_type == 2 && createForm.spec.length > 0">
              <a-form-item :label-col-flex="labelColFlex">
                <a-table :data="batchForm" style="width: 100%" :pagination="false">
                  <template #columns>
                    <a-table-column title="批量设置" align="left" :width="120">
                      <template #cell="{ record, rowIndex }"> <a-button size="mini"
                          @click="onBatchFormData">确定设置</a-button></template>
                    </a-table-column>
                    <template v-for="(item, index) in specForm" :key="index">
                      <a-table-column :data-index="item.key" :title="item.name" align="left" header-cell-class="te"
                        :width="120">
                        <template #cell="{ record, rowIndex }">
                          <a-form-item row-class="mb0" hide-asterisk hide-label :field="item.required
                            ? `batchForm[${rowIndex}].${item.key}`
                            : ''
                            " validate-trigger="blur">
                            <template v-if="item.type && item.type == 'input'">
                              <a-input aria-hidden v-model="record[item.key]" size="mini" />
                            </template>
                            <template v-if="item.type && item.type == 'input-number'">
                              <a-input-number aria-hidden v-model="record[item.key]" size="mini"
                                :precision="item.precision ? item.precision : 0" />
                            </template>

                            <template v-else-if="item.type && item.type == 'image'">
                              <div class="spec-table-column-img">
                                <upload-btn v-model="record[item.key]" width="40px" height="40px"
                                  count="1"></upload-btn>
                              </div>

                            </template>

                            <template v-else>
                              <template v-if="record[item.key].image">
                                <a-image width="40px" height="40px" :src="record[item.key].image" fit="contain" />
                              </template>
                              <span class="ml5 te">
                                {{ record[item.key].name }}
                              </span>
                            </template>
                          </a-form-item>
                        </template>
                      </a-table-column>
                    </template>
                  </template>
                </a-table>
              </a-form-item>
            </div>

            <div class="mt10" v-if="createForm.spec_type == 2">
              <a-form-item :label-col-flex="labelColFlex" field="spec" :rules="[{ validator: specValid }]">
                <template v-if="createForm.spec.length > 0">
                  <a-table :data="createForm.spec" style="width: 100%" :pagination="false">
                    <template #columns>
                      <template v-for="(item, index) in headColumns" :key="index">
                        <a-table-column :data-index="item.key" :title="item.name" align="left" header-cell-class="te"
                          :width="120">
                          <template #cell="{ record, rowIndex }">
                            <a-form-item row-class="mb0" hide-asterisk hide-label :field="item.required
                              ? `spec[${rowIndex}].${item.key}`
                              : ''
                              " :rules="item.required ? [{ required: true, message: `请输入${item.name}`, }] : []"
                              validate-trigger="blur">
                              <template v-if="item.type && item.type == 'input'">
                                <a-input aria-hidden v-model="record[item.key]" size="mini" />
                              </template>
                              <template v-if="item.type && item.type == 'input-number'">
                                <a-input-number aria-hidden v-model="record[item.key]" size="mini"
                                  :precision="item.precision ? item.precision : 0" />
                              </template>

                              <template v-else-if="item.type && item.type == 'image'">
                                <div class="spec-table-column-img">
                                  <upload-btn v-model="record[item.key]" width="40px" height="40px"
                                    count="1"></upload-btn>
                                </div>

                              </template>

                              <template v-else>
                                <template v-if="record[item.key].image">
                                  <a-image width="40px" height="40px" :src="record[item.key].image" fit="contain" />
                                </template>
                                <span class="ml5 te">
                                  {{ record[item.key].name }}
                                </span>
                              </template>
                            </a-form-item>
                          </template>
                        </a-table-column>
                      </template>
                    </template>
                  </a-table>
                </template></a-form-item>
            </div>
          </template>
        </div>
        <a-form-item :label-col-flex="labelColFlex" field="content" v-show="tabIndex === 2">
          <div class="flex start mt30">
            <div class="goods-content-editor mr50">
              <editor ref="editorRef" height="600px" v-model="createForm.content"></editor>
            </div>
          </div>
        </a-form-item>
        <div class="form-main" v-show="tabIndex === 3">
          <div class="form-main-item">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="配送方式：" field="delivery_type">
                <a-checkbox-group v-model="createForm.delivery_type">
                  <template v-for="(item, index) in DeliveryTypeEnum" :key="index">
                    <a-checkbox :value="item.value">{{
                      item.name
                    }}</a-checkbox>
                  </template>
                </a-checkbox-group>
              </a-form-item>
            </div>
          </div>
          <div class="form-main-item" v-if="$utils.inArray('express', createForm.delivery_type)">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="运费设置：" field="delivery_shipping">
                <a-radio-group v-model="createForm.delivery_shipping">
                  <template v-for="(item, index) in DeliveryShippingEnum" :key="index">
                    <a-radio :value="item.value">{{ item.name }}</a-radio>
                  </template>
                </a-radio-group>
              </a-form-item>
            </div>
          </div>

          <div class="form-main-item" v-if="createForm.delivery_shipping == 2">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="运费：" prop="delivery_shipping_price"
                class="form-main-item-input">
                <a-input-number v-model="createForm.delivery_shipping_price" :precision="2" placeholder="请输入运费">
                  <template #suffix> 元 </template>
                </a-input-number>
              </a-form-item>
            </div>
          </div>
          <div class="form-main-item" v-if="createForm.delivery_shipping == 3">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="运费模板：" field="delivery_shipping_temp_id"
                class="form-main-item-input">
                <a-select v-model="createForm.delivery_shipping_temp_id" placeholder="请选择运费模版">
                  <a-option v-for="item in templatesData" :key="item.id" :label="item.name" :value="item.id" />
                </a-select>
              </a-form-item>
            </div>
            <div class="ml20" v-permission="'store-shipping_templates-create'">
              <a-button type="text" @click="onCreateShippingTemplate">新增运费模版</a-button>
            </div>
          </div>
        </div>

        <div class="form-main" v-show="tabIndex === 4">
          <div class="form-main-item">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="商品服务" field="goods_server">
                <componentStoreGoodsSelectServer v-model="createForm.goods_server"></componentStoreGoodsSelectServer>
              </a-form-item>
            </div>
          </div>
          <div class="form-main-item">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="是否限购" field="buy_limit_status">
                <a-switch v-model="createForm.buy_limit_status" inline-prompt type="round" :checked-value="1"
                  :unchecked-value="0">
                  <template #checked>
                    开启
                  </template>
                  <template #unchecked>
                    关闭
                  </template>
                </a-switch>
              </a-form-item>
            </div>
          </div>
          <div class="form-main-item" v-if="createForm.buy_limit_status">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="限购类型" field="buy_limit_type">
                <a-radio-group v-model="createForm.buy_limit_type">
                  <a-radio :value="0">单次限购</a-radio>
                  <a-radio :value="1">长期限购</a-radio>
                </a-radio-group>
                <template #extra>
                  <div>单次限购是限制每次下单最多购买的数量</div>
                  <div>长期限购是限制一个用户总共可以购买的数量</div>
                </template>
              </a-form-item>
            </div>
          </div>

          <div class="form-main-item" v-if="createForm.buy_limit_status">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="限购数量" field="buy_limit">
                <a-input-number v-model="createForm.buy_limit" :min="0" mode="button"
                  placeholder="请输入限购数量"></a-input-number>
              </a-form-item>
            </div>
          </div>


          <div class="form-main-item">
            <div class="form-main-item-input">

              <a-form-item :label-col-flex="labelColFlex" label="是否起售要求" field="buy_least_status">
                <a-switch v-model="createForm.buy_least_status" inline-prompt type="round" :checked-value="1"
                  :unchecked-value="0">
                  <template #checked>
                    开启
                  </template>
                  <template #unchecked>
                    关闭
                  </template>
                </a-switch>
              </a-form-item>
            </div>
          </div>
          <div class="form-main-item" v-if="createForm.buy_least_status">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="起售要求类型" field="buy_least_type">
                <a-radio-group v-model="createForm.buy_least_type">
                  <a-radio :value="0">单次起售</a-radio>
                  <a-radio :value="1">叠加起售</a-radio>
                </a-radio-group>
                <template #extra>
                  <div>单次起售是要求每次下单最少购买的数量</div>
                  <div>叠加起售是要求一个用户最少购买的数量</div>
                </template>
              </a-form-item>
            </div>
          </div>
          <div class="form-main-item" v-if="createForm.buy_least_status">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="限购数量" field="buy_least">
                <a-input-number v-model="createForm.buy_least" :min="0" mode="button"
                  placeholder="请输入起售数量"></a-input-number>
              </a-form-item>
            </div>
          </div>
          <div class="form-main-item">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="虚拟销量" field="sales_initial">
                <a-input-number v-model="createForm.sales_initial" :min="0" mode="button"
                  placeholder="请输入虚拟销量"></a-input-number>
              </a-form-item>
            </div>
          </div>
          <div class="form-main-item">
            <div class="form-main-item-input">
              <a-form-item :label-col-flex="labelColFlex" label="排序" field="sort">
                <a-input-number v-model="createForm.sort" :min="0" mode="button" placeholder="请输入排序"></a-input-number>
              </a-form-item>
            </div>
          </div>
        </div>
        <a-form-item :label-col-flex="labelColFlex" label="">
          <a-space class="mt50 mb50">
            <template v-if="tabIndex > 1">
              <a-button @click="step(0)">上一步</a-button>
            </template>
            <template v-if="tabIndex < 6">
              <a-button @click="step(1)">下一步</a-button>
            </template>
            <a-button v-if="tabIndex == 6 || true" :loading="btnLoading" :disabled="btnLoading" @click="onSave()"
              type="primary">保存</a-button>
          </a-space>
        </a-form-item>
      </a-form>


      <!-- 添加运费模版组件 -->
      <storeShippingTemplatesCreate ref="storeShippingTemplatesCreateRef" @success="toShippingTemplatesInit">
      </storeShippingTemplatesCreate>

    </template>
  </layout-body-content>
</template>
<script lang="ts" setup>
import { createStoreGoodsApi, createStoreGoodsSpecApi, getStoreGoodsDetailApi, updateStoreGoodsApi } from "@/api/store/goods";

import { getStoreShippingTemplatesSelectApi } from "@/api/store/shipping-templates";

import storeShippingTemplatesCreate from "./../shipping-templates/create.vue";
import storeGoodsSpec from "@/components/store/goods/spec-value.vue";
import componentStoreGoodsSelectLabel from "@/components/store/goods/select-label.vue";
import componentStoreGoodsSelectUnit from "@/components/store/goods/select-unit.vue";
import componentStoreGoodsSelectCategory from "@/components/store/goods/select-category.vue";
import componentStoreGoodsSelectServer from "@/components/store/goods/select-server.vue";
import componentStoreGoodsSelectSpecTemplates from "@/components/store/goods/select-spec-templates.vue";

import router from "@/router";
import { Result, ResultError, EnumItemType } from "@/types";
import { ref, onMounted, reactive, getCurrentInstance, watch } from "vue";
import { useEnumStore } from "@/store";

const specTypeEnum = ref<EnumItemType[]>(useEnumStore().getEnumItem("store.goods.SpecTypeEnum"));

const DeliveryTypeEnum = ref<EnumItemType[]>(useEnumStore().getEnumItem("store.DeliveryTypeEnum"));

const DeliveryShippingEnum = ref<EnumItemType[]>(useEnumStore().getEnumItem("store.DeliveryShippingEnum"));

const {
  proxy,
  proxy: { $message, $utils },
} = getCurrentInstance() as any;

const labelColFlex = ref<string>("120px");

const initLoading = ref<boolean>(true);

const tabList = ref<EnumItemType[]>([{
  value: 0,
  name: "基础信息"
}, {
  value: 1,
  name: "规格库存"
}, {
  value: 2,
  name: "商品详情"
}, {
  value: 3,
  name: "物流设置"
}, {
  value: 4,
  name: "营销设置"
}])



const tabIndex = ref<number>(0);

const tabChange = (val: number | string) => {
  if (val == 3) {
    proxy?.$refs["editorRef"]?.setFocus();
  }
  tabIndex.value = Number(val)
  // step(Number(val) > tabIndex.value ? 2 : 1)
};

const specForm = ref<
  Array<{
    name: string;
    key: string;
    type?: string;
    unit?: string;
    required?: boolean;
    input_type?: string;
    is_money?: boolean;
    precision?: number | unknown | any;
  }>
>([
  {
    name: "图片",
    key: "goods_image",
    type: "image",
    precision: null,
  },
  {
    name: "售价",
    key: "price",
    type: "input-number",
    precision: 2,
    unit: "元",
    required: true,
    is_money: true,
  },
  {
    name: "成本价",
    key: "cost_price",
    type: "input-number",
    precision: 2,
    unit: "元",
    required: true,
    is_money: true,
  },
  {
    name: "市场价",
    key: "market_price",
    type: "input-number",
    precision: 2,
    unit: "元",
    required: true,
    is_money: true,
  },
  {
    name: "库存",
    key: "stock",
    type: "input-number",
    precision: 0,
    required: true,
  },
  {
    name: "商品编号",
    key: "bar_code",
    type: "input",
    input_type: "text",
    precision: null,
  },
  {
    name: "重量(KG)",
    key: "weight",
    type: "input-number",
    precision: 1
  },
  {
    name: "体积(m³)",
    key: "volume",
    type: "input-number",
    precision: 1
  },
]);

const createRef = ref<HTMLElement>();

interface specFormValueType {
  goods_image: string;
  price: number | unknown;
  cost_price: number | unknown,
  market_price: number | unknown,
  stock: number | unknown,
  bar_code: string,
  weight: number | unknown,
  volume: number | unknown,
}

const specFormValue = ref<specFormValueType>({
  goods_image: "",
  price: null,
  cost_price: null,
  market_price: null,
  stock: null,
  bar_code: "",
  weight: null,
  volume: null,
});

const createForm = ref<any>({
  category_id: [],
  goods_name: "",
  goods_unit: null,
  goods_code: "",
  goods_images: "",
  goods_label: [],
  spec_type: 1,
  spec: [
    {
      ...specFormValue.value,
    },
  ],
  delivery_type: ["express"],
  delivery_shipping: 1,
  delivery_shipping_temp_id: null,
  delivery_shipping_price: "",
  content: "",
  sales_initial: 0,
  sort: 0,
  keywords: "",
  selling_point: "",
  goods_server: [],
  buy_limit_status: 0,
  buy_limit: null,
  buy_limit_type: 0,
  buy_least_status: 0,
  buy_least: 0,
  buy_least_type: 0,
});

const batchForm = ref<specFormValueType[]>([
  {
    ...specFormValue.value
  }
])

// 修改的规格数据
const updateSpecData = ref<any>([]);

const contentValid = (value: string, cb: any) => {
  if (value === "" || value === "<p><br></p>") {
    cb("商品详细不能为空");
  } else {
    cb();
  }
};
const deliveryPriceValid = (value: string, cb: any) => {
  if (
    $utils.inArray("slef_mention", createForm.value.delivery_type) &&
    createForm.value.delivery_shipping == 2 &&
    value === ""
  ) {
    cb(false);
  } else {
    cb();
  }
};
const specValid = (value: any, cb: any) => {
  if (createForm.value.spec_type == 2 && createForm.value.spec.length == 0) {
    cb("多规格不能为空");
  } else {
    cb();
  }
};
const buyLimitValid = (value: any, cb: any) => {
  if (
    createForm.value.buy_limit_status &&
    createForm.value.buy_least_status &&
    Number(createForm.value.buy_least) > Number(createForm.value.buy_limit)
  ) {
    cb("限购不能小于起购要求!");
  } else {
    cb();
  }
};

const createRules: any = reactive({
  category_id: [{ required: true, message: "请选择商品分类" }],
  goods_name: [{ required: true, message: "请输入商品名称！" }],
  delivery_type: [{ required: true, message: "请选择配送方式！" }],
  goods_images: [{ required: true, message: "商品图片不能为空！" }],
  content: [{ validator: contentValid, message: "商品详细不能为空！" }],
  delivery_shipping_price: [
    { validator: deliveryPriceValid, message: "请输入固定邮费！" },
  ],
  buy_limit: [{ validator: buyLimitValid }],
  buy_least: [{ validator: buyLimitValid }],
});


const operation = ref<string>("create");

const operationId = ref<number | string>(0);

const btnLoading = ref<boolean>(false);

const toInit = () => {
  // toShippingTemplatesInit();
  if (operationId.value != 0) {
    getStoreGoodsDetailApi({
      id: operationId.value,
    })
      .then((res: Result) => {
        var goods = res.data.detail;
        createForm.value.goods_name = goods.goods_name;
        createForm.value.goods_images = goods.goods_images;
        createForm.value.goods_unit = goods.goods_unit || null;
        createForm.value.keywords = goods.keywords;
        createForm.value.sales_initial = goods.sales_initial;
        createForm.value.selling_point = goods.selling_point;
        createForm.value.sort = goods.sort;
        createForm.value.delivery_shipping = goods.delivery_shipping.value;
        createForm.value.delivery_shipping_price =
          goods.delivery_shipping_price;
        createForm.value.delivery_shipping_temp_id =
          goods.delivery_shipping_temp_id;
        createForm.value.delivery_type = goods.delivery_type;
        createForm.value.spec_type = goods.spec_type.value;

        createForm.value.buy_limit_status = goods.buy_limit_status;
        createForm.value.buy_limit = goods.buy_limit;
        createForm.value.buy_limit_type = goods.buy_limit_type;
        createForm.value.buy_least_status = goods.buy_least_status;
        createForm.value.buy_least = goods.buy_least;
        createForm.value.buy_least_type = goods.buy_least_type;

        if (createForm.value.spec_type == 2) {
          updateSpecData.value = goods.sku;
          specData.value = res.data.spec;
          getAttrForm(res.data.spec_attr);
        } else {
          createForm.value.spec = goods.sku;
        }
        createForm.value.content = goods.content;

        proxy?.$refs["editorRef"]?.setContent(goods.content);
        createForm.value.category_id = $utils.getArrayColumnByKey(goods.category, "id");

        createForm.value.goods_label = $utils.getArrayColumnByKey(
          goods.label,
          "id"
        );
        createForm.value.goods_server = $utils.getArrayColumnByKey(
          goods.server,
          "id"
        );

        initLoading.value = false;
      })
      .catch((err: ResultError) => {
        $utils.errorMsg(err);
        router.back();
        initLoading.value = false;
      });
  } else {
    initLoading.value = false;
  }
};

const stepFields = ref<any>([
  [],
  ["category_id", "goods_name", "goods_images", "goods_label", "goods_unit"],
  [],
  ["content"],
  ["delivery_type"],
]);

const step = (type: number) => {
  if (type == 1) {
    tabIndex.value++;
    return;
    if (stepFields.value[tabIndex.value]) {
      stepFields.value[tabIndex.value].forEach((item: any, index: number) => {
        if (typeof item == "object" && item.spec) {
          stepFields.value[tabIndex.value] = [];
          getStepFieldsRules();
        }
      });
      proxy?.$refs['createRef'].validateField(
        stepFields.value[tabIndex.value],
        (valid: boolean) => {
          if (!valid) {
            tabIndex.value++;
          }
        }
      );
    } else {
      tabIndex.value++;
    }
  } else {
    tabIndex.value--;
  }
};

const getStepFieldsRules = () => {
  stepFields.value[2] = [];
  createForm.value.spec.forEach((obj: any, idx: number) => {
    for (let i in obj) {
      var specItem = $utils.getArrayItemByKeyValue(specForm.value, "key", i);
      if (specItem.required) {
        stepFields.value[2].push(`spec[${idx}].[${i}]`);
      }
    }
  });
  if (createForm.value.spec_type == 2) {
    stepFields.value[2].push(`spec`);
  }
};

const specName = ref<string>("");

const specData = ref<any[]>([]);

const onCreateSpecName = () => {
  console.log(specData.value)
  if (specName.value == "") {
    $utils.errorMsg("请输入规格名");
    return false;
  }
  if (specName.value.indexOf(" ") !== -1) {
    $utils.errorMsg("规格名不能包含空格");
    return false;
  }
  specData.value.push({
    name: specName.value,
    values: [],
  });
  specName.value = "";
};


const onSaveSpec = () => {
  if (specData.value.length == 0) {
    $utils.errorMsg("无规格属性");
    return false;
  }
  createStoreGoodsSpecApi({ spec: specData.value })
    .then((res: Result) => {
      getAttrForm(res.data);
      $utils.successMsg(res.message);
    })
    .catch((err: ResultError) => {
      $utils.errorMsg(err);
    });
  // var specList = $utils.getGoodsSpecFormat(specData.value);
  // getAttrForm(specList, formEl);
};

const headColumns = ref<
  Array<{
    name: string;
    key: string;
    type?: string;
    required?: boolean;
    input_type?: string;
    precision?: number | unknown | any;
  }>
>([]);

const getAttrForm = (
  specList: {
    attrs: Array<{
      name: string;
      image: string;
      id: string;
    }>;
    names: string[];
  }
) => {
  var skuValue = [] as Array<any>;
  headColumns.value = [];
  if (specList.attrs.length == 0) {
    $utils.errorMsg("规格值不能为空");
    return false;
  }
  specList.names.forEach((item: string, index: number) => {
    headColumns.value.push({
      name: item,
      key: "attr" + index
    });
  });
  var headArr = specForm.value;
  headArr.forEach((item: { name: string; key: string; type?: string }) => {
    headColumns.value.push(item);
  });
  specList.attrs.forEach(
    (
      item: {
        name: string;
        image: string;
        id: string;
      },
      index: number
    ) => {
      var attrs =
        String(item.id).indexOf(":") !== -1 ? item.id.split(":") : [item.id],
        names = item.name.split(","),
        attrImage: any = item.image?.split(",") || [],
        attrObj: any = {};
      attrObj["sku_attr"] = item.id;
      attrObj["sku_attr_text"] = item.name;
      for (let i = 0; i < names.length; i++) {
        attrObj = Object.assign(
          {
            ["attr" + i]: {
              image: attrImage[i] || null,
              name: names[i],
              attrs: attrs[i] || "",
            },
          },
          attrObj
        );
      }
      skuValue[index] = Object.assign({ ...specFormValue.value }, attrObj);
    }
  );
  createForm.value.spec = skuValue;
  proxy?.$refs['createRef'].validateField(["spec"], (valid: boolean) => { });
};


const storeShippingTemplatesCreateRef = ref<HTMLElement>();

const onCreateShippingTemplate = () => {
  proxy?.$refs["storeShippingTemplatesCreateRef"]?.open();
};

const templatesData = ref<any>([]);

const toShippingTemplatesInit = () => {
  getStoreShippingTemplatesSelectApi()
    .then((res: Result) => {
      templatesData.value = res.data;
    })
    .catch((err: ResultError) => {
      $utils.errorMsg(err);
    });
};

const storeGoodsSpecRef = ref<HTMLElement>();

const specTemplatesItem = ref<any>({});

const specTemplatesId = ref<number | undefined>();

const onChangeStoreGoodsSelectSpecTemplates = (item: any) => {
  if (!item) {
    $utils.errorMsg("请先选择规格模版!");
    return false;
  }
  specTemplatesItem.value = item;
  var item = specTemplatesItem.value;
  specData.value = $utils.toArray(item.attrs) || [];
  proxy?.$refs["storeGoodsSpecRef"]?.refresh(specData.value);
}


onMounted(() => {
  let query = router.currentRoute.value.query;
  if (query && query.id) {
    operationId.value = String(query.id);
    operation.value = "编辑";
  }
  toInit();
});


// 提交
const onSave = () => {
  proxy?.$refs['createRef']?.validate((valid: any, fields: any) => {
    if (!valid) {
      if (btnLoading.value) {
        return;
      }
      btnLoading.value = true;
      let operationApi: any = null;
      if (operation.value == "create") {
        operationApi = createStoreGoodsApi(createForm.value);
      } else {
        operationApi = updateStoreGoodsApi(
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
          $message.success(res.message);
          btnLoading.value = false;
          router.push({
            path: "/store/goods/list",
          });
        })
        .catch((err: ResultError) => {
          $utils.errorMsg(err);
          btnLoading.value = false;
        });
    } else {
      var errFields: string[] = [];
      for (let i in fields) {
        errFields.push(i);
      }
      getStepFieldsRules();
      stepFields.value.forEach((item: any, index: number) => {
        if ($utils.inArray(errFields[0], item)) {
          tabIndex.value = index;
        }
      });
    }
  });
};


watch(
  () => createForm.value.spec_type,
  (val) => {
    if (createForm.value.spec_type == 1) {
      createForm.value.spec = [
        {
          ...specFormValue.value,
        },
      ];
    } else {
      createForm.value.spec = updateSpecData.value;
    }
  },
  { deep: true }
);


const onBatchFormData = () => {
  createForm.value.spec.forEach((item: any, index: number) => {
    if (batchForm.value[0].goods_image) {
      item.goods_image = batchForm.value[0].goods_image;
    }
    if (batchForm.value[0].bar_code) {
      item.bar_code = batchForm.value[0].bar_code;
    }
    if (batchForm.value[0].cost_price) {
      item.cost_price = batchForm.value[0].cost_price;
    }
    if (batchForm.value[0].market_price) {
      item.market_price = batchForm.value[0].market_price;
    }
    if (batchForm.value[0].price) {
      item.price = batchForm.value[0].price;
    }
    if (batchForm.value[0].stock) {
      item.stock = batchForm.value[0].stock;
    }
    if (batchForm.value[0].volume) {
      item.volume = batchForm.value[0].volume;
    }
    if (batchForm.value[0].weight) {
      item.weight = batchForm.value[0].weight;
    }
  })
  console.log(batchForm.value, createForm.value.spec)
}
</script>


<style scoped>
.form-main {
  margin: 30px 0;
}

.form-main-item {
  width: 600px;
  display: flex;
}

.form-main-item-input {
  width: 500px;
}

.form-main-item-btn {
  font-size: 12px;
  line-height: 32px;
  color: var(--base-default);
  cursor: pointer;
  white-space: nowrap;
}

.goods-content-editor {
  width: calc(100% - 380px);
  max-width: 800px;
  min-width: 360px;
}

.form-main-item-small {
  width: 200px;
}
</style>