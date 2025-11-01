import { request } from '@/utils/request/default'

export const getStoreGoodsSpecTemplatesListApi = (params:any) => {
    return request({
        url: 'store.goodsSpecTemplates/list',
        method: 'GET',
        params
    })
}
export const getStoreGoodsSpecTemplatesSelectApi = () => {
    return request({
        url: 'store.goodsSpecTemplates/select',
        method: 'GET'
    })
}

export const createStoreGoodsSpecTemplatesApi = (data:any) => {
    return request({
        url: 'store.goodsSpecTemplates/create',
        method: 'POST',
        data
    })
}

export const updateStoreGoodsSpecTemplatesApi = (data:any) => {
    return request({
        url: `store.goodsSpecTemplates/update`,
        method: 'PUT',
        data
    })
}
export const deleteStoreGoodsSpecTemplatesApi = (data:{ids:number[]|string[]}) => {
    return request({
        url: `store.goodsSpecTemplates/delete`,
        method: 'DELETE',
        data
    })
}

export const getStoreGoodsSpecTemplatesDetailApi = (params:{id:number|string}) => {
    return request({
        url: `store.goodsSpecTemplates/detail`,
        method: 'GET',
        params
    })
}
