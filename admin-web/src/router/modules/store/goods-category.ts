export default [
    {
        path: `/store/goods-category/list`,
        name: `storeGoodsCategoryList`,
        component: () => import("@/views/store/goods-category/list.vue"),
        meta: {
            auth: 'store-goods-category-list',
            menu_name: "商品分类管理",
        },
    },
]