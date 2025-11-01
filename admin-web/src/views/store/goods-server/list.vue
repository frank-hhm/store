<template>
    <layout-body-content pageHeader>
        <template v-slot:page-header-left>
            商品服务管理
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

                <a-button v-permission="'store-goods-server-create'" size="small" type="primary" @click="onCreate(0)">
                    新增服务
                </a-button>
                <!-- 添加 -->
                <createGoodsServerComponent ref="createGoodsServerComponentRef" @success="toInit(true)">
                </createGoodsServerComponent>
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
                }" v-model:selectedKeys="selectedIds">
                <template #columns>
                    <a-table-column title="ID" data-index="id" align="center" :width="100">
                        <template #cell="{ record }">
                            {{ record.id }}
                        </template>
                    </a-table-column>
                    <a-table-column title="图标" data-index="icon" :width="60">
                        <template #cell="{ record }">
                            <div class="icon" :class="record.icon"></div>
                        </template>
                    </a-table-column>
                    <a-table-column title="服务名称" data-index="server_name">
                        <template #cell="{ record }">
                            <div>{{ record.server_name }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="服务内容" data-index="desc">
                        <template #cell="{ record }">
                            <span class="text-grey">{{ record.desc }}</span>
                        </template>
                    </a-table-column>
                    <a-table-column title="创建时间" data-index="create_time" align="center" :width="160">
                        <template #cell="{ record }">
                            <div class="text-grey">{{ record.create_time }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="状态" fixed="right" data-index="status" align="center" :width="80">
                        <template #cell="{ record }">
                            <a-switch v-permission-disabled="'store-goods_server-status'" v-model="record.status.value"
                                size="small" type="round" :loading="record.loading" :beforeChange="() => {
                                    return (record.switch = true);
                                }" @change=" onStatusChange($event, record)" :checked-value="1" :unchecked-value="0" />

                        </template>
                    </a-table-column>
                    <a-table-column title="操作" align="center" :width="140">
                        <template #cell="{ record }">
                            <a-space>
                                <a-button @click=" onCreate(record.id)" v-permission="'store-goods_server-update'"
                                    size="small">编辑</a-button>
                                <div v-permission="'store-goods_server-delete'">
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
import { deleteStoreGoodsServerApi, getStoreGoodsServerListApi, updateStatusStoreGoodsServerApi } from "@/api/store/goods-server";
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
    getStoreGoodsServerListApi(obj)
        .then((res: Result) => {
            initLoading.value = false;
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
    deleteStoreGoodsServerApi({
        ids: [id],
    })
        .then((res: Result) => {
            toInit(true);
            $utils.successMsg(res.message);
        })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
        });
};

const onStatusChange = (val: any, row: any) => {
    if (row.switch === true) {
        row.loading = true;
        updateStatusStoreGoodsServerApi({
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
    deleteStoreGoodsServerApi({
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
