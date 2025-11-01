export default [
    {
        path: `/store/goods-unit/list`,
        name: `storeGoodsUnitList`,
        component: () => import("@/views/store/goods-unit/list.vue"),
        meta: {
            auth: 'store-goods-unit-list',
            menu_name: "商品单位管理",
        },
    },
]