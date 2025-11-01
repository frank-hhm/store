export default [
    {
        path: `/store/goods-spec-templates/list`,
        name: `storeGoodsSpecTemplatesList`,
        component: () => import("@/views/store/goods-spec-templates/list.vue"),
        meta: {
            auth: 'store-goods-spec-templates-list',
            menu_name: "商品规格模版管理",
        },
    },
]