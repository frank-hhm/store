<template>
    <layout-body-content pageHeader>
        <template v-slot:page-header-left>
            商品规格模板管理
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
                <a-button v-permission="'store-goods-spec-templates-create'" size="small" type="primary"
                    @click="onCreate(0)">
                    新增规格模版
                </a-button>
                <!-- 添加 -->
                <createGoodsServerComponent ref="createGoodsServerComponentRef" @success="toInit(true)">
                </createGoodsServerComponent>
            </a-space>
        </template>
        <template v-slot:content="{
            height
        }">
            <a-table :loading="initLoading" :data="lists" row-key="id" isLeaf :pagination="false" :row-selection="{
                type: 'checkbox',
                showCheckedAll: true,
                onlyCurrent: false,
            }" v-model:selectedKeys="selectedIds" :expandable="{
                width: 80
            }">
                <template #expand-row="{ record }">
                    <div class="goods-spec-templates-box">
                        <template v-for="(item, index) in record.attrs">
                            <div class="goods-spec-template-item">
                                <div class="tag dark">{{ item.name }}:</div>
                                <div class="goods-spec-template-attrs">
                                    <div class="text-grey" v-for="(items, idx) in item.values">{{ items.name }}、</div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template #columns>

                    <a-table-column title="模版名称" data-index="spec_temp_name">
                        <template #cell="{ record }">
                            <div>{{ record.spec_temp_name }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="模版规格" data-index="attrs">
                        <template #cell="{ record }">
                            <a-space><a-tag v-for="(item, index) in record.attrs">{{ item.name }}</a-tag></a-space>
                        </template>
                    </a-table-column>
                    <a-table-column title="创建时间" data-index="create_time" align="center" :width="160">
                        <template #cell="{ record }">
                            <div class="text-grey">{{ record.create_time }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="操作" align="center" :width="140">
                        <template #cell="{ record }">
                            <a-space>
                                <a-button @click=" onCreate(record.id)"
                                    v-permission="'store-goods-spec-templates-update'" size="small">编辑</a-button>
                                <div v-permission="'store-goods-spec-templates-delete'">
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
        <template #footer>
            <page :listPage="listPage" @change="pageChange"></page>
        </template>
    </layout-body-content>
</template>


<script lang="ts" setup>
import { ref, getCurrentInstance, onMounted, } from "vue";
import createGoodsServerComponent from './create.vue';
import { PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import { deleteStoreGoodsSpecTemplatesApi, getStoreGoodsSpecTemplatesListApi } from "@/api/store/goods-spec-templates";
import { Message } from "@arco-design/web-vue";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const createGoodsServerComponentRef = ref<HTMLElement>()

const onCreate = (id: number) => {
    proxy?.$refs['createGoodsServerComponentRef'].open(id)
}

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


const toInit = (isInit: boolean = false) => {
    if (isInit) {
        listPage.value.page = 1;
    }
    let obj: any = {};
    obj.page = listPage.value.page;
    obj.limit = listPage.value.limit;
    initLoading.value = true;
    getStoreGoodsSpecTemplatesListApi(obj)
        .then((res: Result) => {
            lists.value = res.data.data;
            listPage.value.total = res.data.total;

            setTimeout(() => {
                initLoading.value = false;
            }, 300);
        })
        .catch((error: ResultError) => {
            $utils.errorMsg(error);
            initLoading.value = false;
        });
}

onMounted(() => {
    toInit()
})

const selectedIds = ref<string[] | number[]>([])


const onDelete = (id: number) => {
    deleteStoreGoodsSpecTemplatesApi({
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
    deleteStoreGoodsSpecTemplatesApi({
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
.goods-spec-templates-box{
    display: flex;
    flex-wrap: wrap;
}
.goods-spec-template-item{
    width: 200px;
    background: var(--color-bg-2);
    margin-right: var(--base-padding);
    padding: var(--base-padding);
}
.goods-spec-template-attrs{
    display: flex;
    flex-wrap: wrap;
}

</style>