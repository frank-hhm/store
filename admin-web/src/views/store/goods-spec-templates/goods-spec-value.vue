<template>
    <div class="goods-spec-box" :style="[
        height ? { height: height + 'px' } : {},
    ]" v-if="specData.length > 0">
        <template v-for="(item, index) in specData" :key="index">
            <div class="goods-spec-item">
                <div class="goods-spec-drag icon icon-drag" draggable="true" @dragstart.capture="specDragstart(item)"
                    @dragenter.capture="specDragenter(item, $event)" @dragend.capture="specDragend(item, $event)"
                    @dragover.capture="specDragover($event)"></div>
                <div class="goods-spec-item-box">
                    <div class="flex">
                        <div class="goods-spec-title">
                            规格名
                        </div>
                        <div>
                            <span class="goods-spec-name">{{ item.name }}</span>
                            <span @click="onDeleteSpecName(index)" class="del-spec-value icon icon-shibai1"></span>
                        </div>
                    </div>

                    <div class="mt10">
                        <div class="flex default-append items-center">
                            <div class="goods-spec-title">规格值</div>
                            <a-input class="form-item-small" v-model="specValue[index]" placeholder="请输入规格值">
                                <template #append>
                                    <a-button @click="onCreateSpecValue(index)">
                                        添加
                                    </a-button>
                                </template>
                            </a-input>
                            <div class="flex ml50">
                                <div class="goods-spec-title">
                                    规格图片
                                </div>
                                <div>
                                    <a-switch type="round" size="small" v-model="item.is_img" inline-prompt
                                        :checked-value="1" :unchecked-value="0"
                                        @change="changeImgStatus($event, item)"></a-switch>
                                </div>
                            </div>
                        </div>
                        <div class="goods-spec-value-box" v-if="item.values.length > 0">
                            <template v-for="(items, idx) in item.values" :key="idx">
                                <div class="goods-spec-value-item" :class="item.is_img == 1 ? 'is-image' : ''">
                                    <span class="goods-spec-value-item-drag icon icon-drag" draggable="true"
                                        @dragstart="specValueDragstart(index, items)"
                                        @dragenter="specValueDragenter(index, items, $event)"
                                        @dragend="specValueDragend(index, items, $event)"
                                        @dragover="specValueDragover($event)"></span>
                                    <span v-if="item.is_img == 1" class="mr10">
                                        <upload-btn v-model="items.img" width="46px" height="46px" padding="0"
                                            count="1"></upload-btn>
                                    </span>
                                    <span class="goods-spec-name">{{ items.name }}</span>
                                    <span @click="onDeleteSpecValue(index, idx)"
                                        class="del-spec-value icon icon-shibai1"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
<script lang="ts">
export default {
    name: "storeGoodsSpecValue",
};
</script>
<script  lang="ts" setup >
import { ref, getCurrentInstance, watch } from "vue";
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

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["update:modelValue"]);

const specData = ref<any[]>(props.modelValue);


const onCreateSpecValue = (index: number) => {
    if (specValue.value[index] == "" || specValue.value[index] == undefined) {
        $utils.errorMsg("请输入规格值");
        return false;
    }
    if (specValue.value[index].indexOf(" ") !== -1) {
        $utils.errorMsg("规格值不能包含空格");
        return false;
    }
    specData.value[index].values.push({
        name: specValue.value[index],
    });
    specValue.value[index] = "";
};

const specValue = ref<any>([]);

const onDeleteSpecName = (index: number) => {
    specData.value.splice(index, 1);
};

const onDeleteSpecValue = (index: number, idx: number) => {
    specData.value[index].values.splice(idx, 1);
};

const specOldItem = ref<any>();

const specNewItem = ref<any>();

const specDragstart = (value: any) => {
    specOldItem.value = value;
};

const specDragend = (value: any, e: any) => {
    if (specOldItem.value !== specNewItem.value) {
        let oldIndex = specData.value.indexOf(specOldItem.value);
        let newIndex = specData.value.indexOf(specNewItem.value);

        let newItems = [...specData.value];
        newItems.splice(oldIndex, 1);
        newItems.splice(newIndex, 0, specOldItem.value);
        specData.value = [...newItems];
    }
};

const specDragenter = (value: any, e: any) => {
    specNewItem.value = value;
    e.preventDefault();
};

const specDragover = (e: any) => {
    e.preventDefault();
};

const specValueOldItem = ref<any>();

const specValueNewItem = ref<any>();

const specValueDragstart = (index: number, value: any) => {
    specValueOldItem.value = value;
};

const specValueDragend = (index: number, value: any, e: any) => {
    if (specValueOldItem.value !== specValueNewItem.value) {
        let oldIndex = specData.value[index].values.indexOf(specValueOldItem.value);
        let newIndex = specData.value[index].values.indexOf(specValueNewItem.value);

        let newItems = [...specData.value[index].values];
        newItems.splice(oldIndex, 1);
        newItems.splice(newIndex, 0, specValueOldItem.value);
        specData.value[index].values = [...newItems];
    }
};

const specValueDragenter = (index: number, value: any, e: any) => {
    specValueNewItem.value = value;
    e.preventDefault();
};

const specValueDragover = (e: any) => {
    e.preventDefault();
};

const changeImgStatus = (e: string | number | boolean, item: any) => {
    if (e == 0) {
        item.value.forEach((items: { img: string; name: string }) => {
            items.img = "";
        });
    }
};

const refresh = (data: any = []) => {
    specData.value = data;
};

watch(
    () => specData.value,
    (val) => {
        emit("update:modelValue", val);
    },
    { deep: true }
);

defineExpose({ refresh });
</script>
<style scoped>
.form-item-small {
    width: 200px;
}

/* spac-view */
.goods-spec-box {
    width: 100%;
    max-height: 600px;
    overflow-y: scroll;
    border-radius: var(--base-radius);
    background: var(--color-fill-2);
    padding: 0 10px;
}

.goods-spec-item {
    margin: 10px 0;
    padding: 10px;
    background: var(--color-bg-1);
    border-radius: var(--base-radius);
    width: calc(100% - 20px);
    position: relative;
}

.goods-spec-drag {
    position: absolute;
    cursor: all-scroll;
    text-align: center;
    width: 16px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    top: 0;
    left: 0;
    border-right: 1px solid var(--color-border-1);
    border-top-left-radius: var(--base-radius);
    border-bottom-left-radius: var(--base-radius);
    color: rgba(var(--gray-6));
}

.goods-spec-title {
    width: 60px;
    color: rgba(var(--gray-6));
    text-align: right;
    margin-right: 20px;
}

.goods-spec-name {
    color: var(--color-text-1);
}

.goods-spec-item-box {
    width: calc(100% - 20px);
    margin-left: 20px;
}

.goods-spec-value-box {
    display: flex;
    flex-wrap: wrap;
    margin-top: 10px;
}

.goods-spec-value-item {
    display: flex;
    align-items: center;
    /* background-color: var(--color-fill-2); */
    height: 32px;
    line-height: 32px;
    padding: 0 10px 0 20px;
    margin-right: 10px;
    border-radius: 2px;
    border: 1px solid var(--color-border-1);
    margin-bottom: 10px;
    position: relative;
}

.goods-spec-value-item.is-image {
    height: 50px;
    line-height: 50px;
}

.goods-spec-value-item-drag {
    cursor: all-scroll;
    text-align: center;
    width: 16px;
    height: 32px;
    line-height: 32px;
    font-size: 12px;
    border-right: 1px solid var(--color-border-1);
    color: rgba(var(--gray-6));
    margin-right: 5px;
    position: absolute;
    left: 0;
    top: 0;
}

.goods-spec-value-item.is-image .goods-spec-value-item-drag {
    height: 50px;
    line-height: 50px;
}

.del-spec-value {
    color: rgba(var(--gray-6));
    font-size: 12px;
    margin-left: 10px;
    cursor: pointer;
}

.del-spec-value:hover {
    color: rgba(var(--red-6));
}
</style>