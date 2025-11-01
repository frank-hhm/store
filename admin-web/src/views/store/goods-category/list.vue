<template>
    <layout-body-content pageHeader hideFooter>
        <template v-slot:page-header-left>
            商品分类管理
        </template>
        <template v-slot:page-header-right>
            <a-space>
                <a-tooltip content="刷新">
                    <a-button @click="toInit" size="small"><icon-refresh /></a-button>
                </a-tooltip>
                <a-button size="small" v-permission="'store-goods_cate-create'" type="primary" @click="onCreate(0)">
                   新增分类
                </a-button>
                <createGoodsCateComponent ref="createGoodsCateComponentRef" @success="toInit()">
                </createGoodsCateComponent>
            </a-space>
        </template>
        <template v-slot:content="{
            height
        }">
            <a-table :loading="initLoading" :data="lists" row-key="id" isLeaf :pagination="false"  :scroll="{
                    x: '100%',
                    y: height - 39
                }">
                <template #columns>
                    <a-table-column title="分类名称" data-index="category_name" :width="200">
                        <template #cell="{ record }">
                            <span>{{ record.html }}</span>
                            <span>{{ record.category_name }}</span>
                        </template>
                    </a-table-column>
                    <a-table-column title="分类图标" data-index="icon" :width="100">
                        <template #cell="{ record }">
                            <a-image :src="record.icon" width="30" height="30"></a-image>
                        </template>
                    </a-table-column>
                    <a-table-column title="分类大图" data-index="image" :width="100">
                        <template #cell="{ record }">
                            <a-image :src="record.image" width="40" height="40"></a-image>
                        </template>
                    </a-table-column>
                    <a-table-column title="创建时间" data-index="create_time" align="center" :width="160">
                        <template #cell="{ record }">
                            <div class="text-grey">{{ record.create_time }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="更新时间" data-index="update_time" align="center" :width="160">
                        <template #cell="{ record }">
                            <div class="text-grey">{{ record.update_time }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="状态" fixed="right" data-index="status" align="center" :width="80">
                        <template #cell="{ record }">
                            <a-switch v-permission-disabled="'store-goods_cate-status'" v-model="record.status.value"
                                size="small" type="round" :loading="record.loading" :beforeChange="() => {
                                    return (record.switch = true);
                                }" @change=" onStatusChange($event, record)" :checked-value="1" :unchecked-value="0" />

                        </template>
                    </a-table-column>
                    <a-table-column title="操作" fixed="right" align="center" :width="140">
                        <template #cell="{ record }">
                            <a-space>
                                <a-button @click=" onCreate(record.id)" v-permission="'store-goods_cate-update'"
                                    size="small">编辑</a-button>
                                <div v-permission="'store-goods_cate-delete'">
                                    <a-popconfirm content="确定删除吗？" @ok="onDelete(record.id)">
                                        <template #icon>
                                            <icon-exclamation-circle-fill type="red" />
                                        </template>
                                        <a-button size="small">删除</a-button>
                                    </a-popconfirm>
                                </div>
                            </a-space>
                        </template>
                    </a-table-column>
                </template>
            </a-table>
        </template>
    </layout-body-content>
</template>
<script lang="ts" setup>
import { ref, getCurrentInstance, onMounted, nextTick } from "vue";
import { useSetting } from "@/hooks/useSetting";
import createGoodsCateComponent from './create.vue';

import { deleteStoreGoodsCategoryApi, getStoreGoodsCategoryListApi, updateStatusStoreGoodsCategoryApi } from "@/api/store/goods-category";
import { Result, ResultError } from "@/types";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const createGoodsCateComponentRef = ref<HTMLElement>()

const onCreate = (id: number) => {
    proxy?.$refs['createGoodsCateComponentRef'].open(id)
}

const initLoading = ref<boolean>(false);


const lists = ref<any>([]);

const toInit = () => {
    initLoading.value = true;
    getStoreGoodsCategoryListApi({})
        .then((res: Result) => {
            lists.value = res.data;
            setTimeout(() => {
                initLoading.value = false;
            }, 300);
        })
        .catch((err: ResultError) => {
            initLoading.value = false;
            $utils.errorMsg(err);
        });
};
const onStatusChange = (
    val: number | any,
    row: {
        switch: boolean;
        loading: boolean;
        id: number;
        status:{
            value:number,
            name:string
        }
    }
) => {
    if (row.switch === true) {
        row.loading = true;
        updateStatusStoreGoodsCategoryApi({
            id: row.id,
            status: val,
        })
            .then((res: Result) => {
                row.loading = false;
                toInit();
                $utils.successMsg(res);
            })
            .catch((err: ResultError) => {
                row.loading = false;
                row.status.value = Number(!row.status.value)
                $utils.errorMsg(err);
            });
    }
};

const onDelete = (id: number) => {
    deleteStoreGoodsCategoryApi({
        id,
    })
        .then((res: Result) => {
            toInit();
            $utils.successMsg(res.message);
        })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
        });
};
onMounted(() => {
    toInit();
});
</script>