import { request } from '@/utils/request/default'

export const getStoreGoodsServerListApi = (params:any) => {
    return request({
        url: 'store.goodsServer/list',
        method: 'GET',
        params
    })
}

export const getStoreGoodsServerSelectApi =() => {
    return request({
        url: 'store.goodsServer/select',
        method: 'GET'
    })
}

export const createStoreGoodsServerApi = (data:any) => {
    return request({
        url: 'store.goodsServer/create',
        method: 'POST',
        data
    })
}
export const updateStoreGoodsServerApi = (data:any) => {
    return request({
        url: `store.goodsServer/update`,
        method: 'PUT',
        data
    })
}
export const updateStatusStoreGoodsServerApi = (data:{id:number|string,status:number|string}) => {
    return request({
        url: `store.goodsServer/status`,
        method: 'PUT',
        data
    })
}

export const deleteStoreGoodsServerApi = (data:{ids:number[]|string[]}) => {
    return request({
        url: `store.goodsServer/delete`,
        method: 'DELETE',
        data
    })
}

export const getStoreGoodsServerDetailApi = (params:{id:number|string}) => {
    return request({
        url: `store.goodsServer/detail`,
        method: 'GET',
        params
    })
}