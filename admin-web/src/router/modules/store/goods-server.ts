export default [
    {
        path: `/store/goods-server/list`,
        name: `storeGoodsServerList`,
        component: () => import("@/views/store/goods-server/list.vue"),
        meta: {
            auth: 'store-goods-server-list',
            menu_name: "商品服务管理",
        },
    },
]