export default [
    {
        path: `/store/shipping-templates/list`,
        name: `storeConfigShippingTemplatesList`,
        component: () => import("@/views/store/shipping-templates/list.vue"),
        meta: {
            auth: 'store-shipping-templates-list',
            menu_name: "运费模板管理",
        },
    },
]