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
    {
        path: `/store/goods/create`,
        name: `storeGoodsCreate`,
        component: () => import("@/views/store/goods/create.vue"),
        meta: {
            auth: 'store-goods-create',
            activeMenu:'/store/goods/list',
            menu_name: "商品规格",
        },
    }
]