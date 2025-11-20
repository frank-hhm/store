import { request } from '@/utils/request/default'

export const getStoreGoodsListApi = (params:any) => {
    return request({
        url: 'store.goods/list',
        method: 'GET',
        params
    })
}

export const getStoreGoodsSimpleListApi = (params:any) => {
    return request({
        url: 'store.goods/simpleList',
        method: 'GET',
        params
    })
}

export const getStoreGoodsSimpleSkuListApi = (params:any) => {
    return request({
        url: 'store.goods/simpleSkuList',
        method: 'GET',
        params
    })
}


export const createStoreGoodsSpecApi = (data:any) => {
    return request({
        url: 'store.goods/createSpec',
        method: 'POST',
        data
    })
}
export const createStoreGoodsApi = (data:any) => {
    return request({
        url: 'store.goods/create',
        method: 'POST',
        data
    })
}
export const updateStoreGoodsApi = (data:any) => {
    return request({
        url: `store.goods/update`,
        method: 'PUT',
        data
    })
}
export const deleteStoreGoodsApi = (data:{id:number|string}) => {
    return request({
        url: `store.goods/delete`,
        method: 'DELETE',
        data
    })
}
export const destroyStoreGoodsApi = (data:{id:number|string}) => {
    return request({
        url: `store.goods/destroy`,
        method: 'DELETE',
        data
    })
}
export const updateStatusStoreGoodsApi = (data:{id:number|string,status:number|string}) => {
    return request({
        url: `store.goods/status`,
        method: 'PUT',
        data
    })
}

export const getStoreGoodsDetailApi = (params:{id:number|string}) => {
    return request({
        url: `store.goods/detail`,
        method: 'GET',
        params
    })
}
