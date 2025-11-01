<template>
  <div class="region-select-body">
    <a-modal title="选择区域" @BeforeCancel="close" width="1000px" :top="useSetting().ModalTop" class="modal"
      v-model:visible="visible" :align-center="false" title-align="start" render-to-body>

      <div class="region-list" v-loading="initLoading">
        <template v-if="visible">
          <template v-for="(item, index) in lists" :key="index">
            <div class="region-item">
              <a-dropdown trigger="hover" v-if="!$utils.isPropertyExist(item.id, disable.treeData) ||
                !disable.treeData[item.id].isAllCitys
                ">
                <div class="region-item-name">
                  <a-checkbox :model-value="$utils.inArray(item.id, provinceChecked)"
                    @change="onCheckedProvince(item.id, $event)">
                    {{ item.name }}
                  </a-checkbox>
                </div>
                <template #content>
                  <div class="region-citys">
                    <template v-for="(city, idx) in item.city" :key="idx">
                      <div class="region-citys-item" v-if="!$utils.inArray(city.id, disable.citys)">
                        <a-checkbox :model-value="$utils.inArray(city.id, citysChecked)"
                          @change="onCheckedCity(item.id, city.id, $event)">
                          {{ city.name }}</a-checkbox>
                      </div>
                    </template>
                  </div>
                </template>
              </a-dropdown>
              <template v-else>
                <div class="region-item-name">
                  <a-checkbox :disabled="true">
                    {{ item.name }}
                  </a-checkbox>
                </div>
              </template>
            </div>
          </template>
        </template>
      </div>
      <template #footer>
        <a-space>
          <a-button @click="close">取消</a-button>
          <a-button @click="onCheckAll(false)">清空</a-button>
          <a-button @click="onCheckAll(true)">全选</a-button>
          <a-button type="primary" @click="saveOk">确定</a-button>
        </a-space>
      </template>
    </a-modal>
  </div>
</template>
<script lang="ts">
export default {
  name: "select-region-modal",
};
</script>
<script setup lang="ts">
import { ref, getCurrentInstance, nextTick } from "vue";
import { getTreeListApi } from "@/api/region";
import { Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";

const {
  proxy: { $utils },
  internalInstance,
} = getCurrentInstance() as any;

const props = withDefaults(
  defineProps<{
    lists?: any;
  }>(),
  {
    lists: [],
  }
);

const emit = defineEmits(["change"]);

const visible = ref<boolean>(false);

const initLoading = ref<boolean>(true);

const toInit = () => {
  initLoading.value = false;
};

const checkedData = ref<any>();

const provinceChecked = ref<number[]>([]);

const citysChecked = ref<number[]>([]);

const checkAll = ref<boolean>(false);

const editIndex = ref<number>(-1);

const regionField = ref<string>();

interface disableType {
  province?: number[];
  citys?: number[];
  treeData?: any;
}

// 禁止选择的地区id集
const disable = ref<disableType>({
  province: [],
  citys: [],
  treeData: [],
});

const onCheckedProvince = (id: number, val: boolean | any) => {
  checkedProvince(id, val);
};

const checkedProvince = (provinceId: number, checked: boolean) => {
  // 更新省份选择
  var index = provinceChecked.value.indexOf(provinceId);
  if (!checked) {
    index > -1 && provinceChecked.value.splice(index, 1);
  } else {
    index === -1 && provinceChecked.value.push(provinceId);
  }
  // 更新城市选择
  var cityIds = props.lists[provinceId].city;
  for (var cityIndex in cityIds) {
    if (cityIds.hasOwnProperty(cityIndex)) {
      var cityId = parseInt(cityIndex);
      var checkedIndex = citysChecked.value.indexOf(cityId);
      if (!checked) {
        checkedIndex > -1 && citysChecked.value.splice(checkedIndex, 1);
      } else {
        checkedIndex === -1 && citysChecked.value.push(cityId);
      }
    }
  }
};

// 选择城市
const onCheckedCity = (
  provinceId: number,
  cityId: number,
  checked: boolean | any
) => {
  nextTick(() => {
    if (!checked) {
      var index = citysChecked.value.indexOf(cityId);
      index > -1 && citysChecked.value.splice(index, 1);
    } else {
      citysChecked.value.push(cityId);
    }
    // 更新省份选中状态
    onUpdateProvinceChecked(provinceId);
  });
};

// 更新省份选中状态
const onUpdateProvinceChecked = (provinceId: number) => {
  var provinceIndex = provinceChecked.value.indexOf(provinceId);
  var isExist = provinceIndex > -1;
  if (!onHasCityChecked(provinceId)) {
    if (isExist) provinceChecked.value.splice(provinceIndex, 1);
  } else {
    if (!isExist) provinceChecked.value.push(provinceId);
  }
};

// 是否存在城市被选中
const onHasCityChecked = (provinceId: number) => {
  var cityIds = props.lists[provinceId].city;
  for (var cityId in cityIds) {
    if (
      cityIds.hasOwnProperty(cityId) &&
      $utils.inArray(parseInt(cityId), citysChecked.value)
    )
      return true;
  }
  return false;
};

// 全选
const onCheckAll = (checked: boolean) => {
  checkAll.value = checked;
  // 遍历能选择的地区
  for (var key in props.lists) {
    if (props.lists.hasOwnProperty(key)) {
      var item = props.lists[key];
      if (
        !$utils.isPropertyExist(item.id, disable.value.treeData) ||
        !disable.value.treeData[item.id].isAllCitys
      ) {
        var provinceId = parseInt(item.id);
        checkedProvince(provinceId, checkAll.value);
      }
    }
  }
};

// 标记不可选的地区
const onDisableRegion = (ignoreFormIndex: number, checkedForms: any) => {
  // 清空禁选地区
  var disableObj = { province: [], citys: [] };
  for (var key in checkedForms) {
    if (checkedForms.hasOwnProperty(key)) {
      if (ignoreFormIndex > -1 && ignoreFormIndex === parseInt(key)) continue;
      var item = checkedForms[key];
      disableObj.province = $utils.arrayMerge(
        disableObj.province,
        item.province
      );
      disableObj.citys = $utils.arrayMerge(disableObj.citys, item.citys);
    }
  }
  disable.value = {
    province: disableObj.province,
    citys: disableObj.citys,
    treeData: $utils.getRegionsTreeData(disableObj, props.lists),
  };
};
const open = async (field: string, checked: any, index: number) => {
  regionField.value = field;

  provinceChecked.value = JSON.parse(
    JSON.stringify(checked[index]?.province || [])
  );
  citysChecked.value = JSON.parse(JSON.stringify(checked[index]?.citys || []));
  onDisableRegion(index, checked);
  editIndex.value = index;
  if (visible.value == true) {
    return;
  }
  toInit();
  visible.value = true;
};

const close = () => {
  visible.value = false;
  provinceChecked.value = [];
  citysChecked.value = [];
  return true
};

const saveOk = () => {
  if (citysChecked.value.length == 0) {
    $utils.errorMsg("区域不能为空！");
    return false;
  }
  emit("change", {
    field: regionField.value,
    province: provinceChecked.value,
    citys: citysChecked.value,
    editIndex: editIndex.value,
  });
  close();
};

defineExpose({ open });
</script>
<style scoped>
.region-list {
  display: flex;
  flex-wrap: wrap;
  height: 260px;
  overflow-y: scroll;
}

.region-item {
  width: 170px;
  position: relative;
}

.region-item-name {
  cursor: pointer;
}

.region-item-name:hover {
  color: var(--base-default);
}

.region-citys {
  padding: 10px;
  display: flex;
  flex-wrap: wrap;
  min-width: 100px;
  max-width: 300px;
}

.region-citys-item {
  margin-top: 10px;
  margin-right: 10px;
}
</style>