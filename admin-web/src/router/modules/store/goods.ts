export default [
    {
        path: `/store/goods/list`,
        name: `storeGoodsList`,
        component: () => import("@/views/store/goods/list.vue"),
        meta: {
            auth: 'store-goods-list',
            menu_name: "商品管理",
        },
    },
]