<template>
    <layout-body-content pageHeader>
        <template v-slot:page-header-left>
            商品管理
        </template>
        <template v-slot:page-header-right>
            <a-space>
                <a-tooltip content="刷新">
                    <a-button @click="toInit()" size="small"><icon-refresh /></a-button>
                </a-tooltip>
                <a-dropdown position="bottom" :disabled="onBatchDeleteLoading">
                    <a-button size="small"><icon-more /></a-button>
                    <template #content>
                        <a-doption @click="onBatchDelete">批量删除</a-doption>
                    </template>
                </a-dropdown>
                <a-button v-permission="'store-goods-create'" size="small" type="primary" @click="onCreate(0)">
                    新增商品
                </a-button>
            </a-space>
        </template>
        <template v-slot:content="{
            height
        }">
            <a-table :loading="initLoading" :data="lists" row-key="id" isLeaf :pagination="false" :scroll="{
                x: '100%',
                y: height - 39
            }" :row-selection="{
                type: 'checkbox',
                showCheckedAll: true,
                onlyCurrent: false,
            }" v-model:selectedKeys="selectedIds" :expandable="{
                width: 80
            }">
                <template #expand-row="{ record }">

                    <div class="goods-list-box">
                        <template v-for="(item, index) in record.sku">
                            <div class="item">
                                <div>
                                    <a-image :width="40" :height="40" v-if="item?.goods_image" :src="item.goods_image"
                                        fit="cover" />
                                    <a-image :width="40" :height="40" v-else :src="record.goods_image" fit="cover" />
                                </div>
                                <div class="ml10" style="width: calc(100% - 40px);">
                                    <div class="flex items-center">
                                        <div class="text-grey">规格:</div>
                                        <div class="tag dark grey">{{ item.sku_attr_text }}</div>
                                    </div>
                                    <div>
                                        <span class="text-grey">市场价:</span>
                                        <span>￥{{ item.market_price }}</span>
                                    </div>
                                    <div>
                                        <span class="text-grey">成本价:</span>
                                        <span>￥{{ item.cost_price }}</span>
                                    </div>
                                    <div>
                                        <span class="text-grey">销售价格:</span>
                                        <span class="text-red">￥{{ item.price }}</span>
                                    </div>
                                    <div>
                                        <span class="text-grey">库存:</span>
                                        <span>{{ item.stock }}</span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template #columns>
                    <a-table-column title="商品分类" data-index="category" :width="120">
                        <template #cell="{ record }">
                            <a-space>
                                <template v-for="(item, index) in record.category" :key="index">
                                    <a-tag>{{ item.category_name }}</a-tag>
                                </template>
                            </a-space>
                        </template>
                    </a-table-column>
                    <a-table-column title="商品图" align="center" data-index="goods_image" :width="80">
                        <template #cell="{ record }">
                            <a-image width="40px" height="40px" :src="record.goods_image" fit="cover" />
                        </template>
                    </a-table-column>
                    <a-table-column title="商品名称" data-index="goods_name" :width="240">
                        <template #cell="{ record }">
                            <div class="goods-name">{{ record.goods_name }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="销量" align="center" data-index="sales_actual" :width="80">
                        <template #cell="{ record }">
                            <span>{{ record.sales_actual }}</span>
                        </template>
                    </a-table-column>
                    <a-table-column title="排序" align="center" data-index="sort" :width="80">
                        <template #cell="{ record }">
                            <span>{{ record.sort }}</span>
                        </template>
                    </a-table-column>
                    <a-table-column title="创建时间" data-index="create_time" align="center" :width="160">
                        <template #cell="{ record }">
                            <div class="text-grey">{{ record.create_time }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="状态" fixed="right" data-index="goods_status" align="center" :width="80">
                        <template #cell="{ record }">
                            <a-switch v-permission-disabled="'store-goods-status'" v-model="record.goods_status.value"
                                size="small" type="round" :loading="record.loading" :beforeChange="() => {
                                    return (record.switch = true);
                                }" @change=" onStatusChange($event, record)" :checked-value="1" :unchecked-value="0" />

                        </template>
                    </a-table-column>
                    <a-table-column title="操作" align="center" :width="240">
                        <template #cell="{ record }">
                            <a-space>
                                <a-button size="mini" @click="onCreate(record.id)"
                                    v-permission="'store-goods-update'">编辑</a-button>
                                <!-- 回收站 -->
                                <a-dropdown>
                                    <a-button size="mini" v-permission="'store-goods-destroy'">更多</a-button>
                                    <template #content>
                                        <a-doption @click="onDestroy(record.id)">永久删除</a-doption>
                                    </template>
                                </a-dropdown>

                                <a-popconfirm ok-text="确定" cancel-text="不用了" content="确定删除吗？" @ok="onDelete(record.id)">
                                    <a-button v-permission="'store-goods-delete'" size="mini">删除</a-button>

                                </a-popconfirm>
                            </a-space>
                        </template>
                    </a-table-column>
                </template>
            </a-table>
        </template>
        <template #footer>
            <page :listPage="listPage" @change="pageChange"></page>
        </template>
    </layout-body-content>
</template>
<script lang="ts" setup>
import { deleteStoreGoodsApi, destroyStoreGoodsApi, getStoreGoodsListApi, updateStatusStoreGoodsApi } from "@/api/store/goods";
import { useSetting } from "@/hooks/useSetting";
import router from "@/router";
import { EnumItemType, PageLimitType, Result, ResultError } from "@/types";
import { Message } from "@arco-design/web-vue";
import { ref, getCurrentInstance, onMounted, watch } from "vue";


const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;


const initLoading = ref<boolean>(false);

const listPage = ref<any>({
    total: 0,
    limit: useSetting().PageLimit.value,
    page: 1,
});

const pageChange = (res: PageLimitType) => {
    listPage.value = res;
    toInit();
};

const lists = ref<any>([]);

// 初始化
const toInit = (initPage: boolean = false) => {
    if (initPage) {
        listPage.value.page = 1;
    }
    let obj: any = {};
    obj.page = listPage.value.page;
    obj.limit = listPage.value.limit;

    initLoading.value = true;
    getStoreGoodsListApi(obj)
        .then((res: Result) => {
            lists.value = res.data.data;
            listPage.value.total = res.data.total;
            initLoading.value = false;
        })
        .catch((err: ResultError) => {
            initLoading.value = false;
            $utils.errorMsg(err);
        });
};

onMounted(() => {
    toInit();
})

const onCreate = (id: number | string = 0) => {
    router.push({
        path: "/store/goods/create",
        query: id ? { id } : {},
    });
};


const onStatusChange = (val: any, row: any) => {
    if (row.switch === true) {
        row.loading = true;
        updateStatusStoreGoodsApi({
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
                toInit();
                $utils.errorMsg(err);
            });
    }
};

const selectedIds = ref<number[] | string[]>([])


const onDelete = (id: number) => {
    deleteStoreGoodsApi({
        ids: [id],
    })
        .then((res: Result) => {
            toInit();
            $utils.successMsg(res.message);
        })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
        });
};


const onDestroy = (id: number) => {
    destroyStoreGoodsApi({
        ids: [id],
    })
        .then((res: Result) => {
            toInit();
            $utils.successMsg(res.message);
        })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
        });
};

const onBatchDeleteLoading = ref<boolean>(false)

const BatchDeleteMessage = ref<any>(null)

const onBatchDelete = () => {
    if (selectedIds.value.length === 0) {
        $utils.errorMsg("请选择需要删除的数据");
        return false;
    }
    BatchDeleteMessage.value = Message.loading({
        id: "BatchDeleteMessage",
        content: '正在提交删除操作,请稍等...'
    })
    onBatchDeleteLoading.value = true;
    deleteStoreGoodsApi({
        ids: selectedIds.value,
    })
        .then((res: Result) => {
            onBatchDeleteLoading.value = false;
            BatchDeleteMessage.value.close()
            $utils.successMsg(res.message);
            toInit(true);
        })
        .catch((err: ResultError) => {
            onBatchDeleteLoading.value = false;
            BatchDeleteMessage.value.close()
            $utils.errorMsg(err);
        });
}
</script>

<style scoped>
.goods-list-box .item {
    float: left;
    max-width: calc(25% - 32px);
    min-width:200px;
    margin-right: 10px;
    margin-bottom: 10px;
    display: flex;
    padding: 10px;
    background: var(--color-bg-1);
    border-radius: var(--base-radius);
}
</style>