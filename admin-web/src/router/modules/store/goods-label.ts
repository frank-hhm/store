export default [
    {
        path: `/store/goods-label/list`,
        name: `storeGoodsLabelList`,
        component: () => import("@/views/store/goods-label/list.vue"),
        meta: {
            auth: 'store-goods-label-list',
            menu_name: "商品标签管理",
        },
    },
]