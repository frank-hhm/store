import { request } from '@/utils/request/default'

export const getStoreGoodsLabelListApi = (params: any) => {
    return request({
        url: 'store.goodsLabel/list',
        method: 'GET',
        params
    })
}
export const getStoreGoodsLabelSelectApi = () => {
    return request({
        url: 'store.goodsLabel/select',
        method: 'GET'
    })
}

export const getStoreGoodsLabelDetailApi = (params: any) => {
    return request({
        url: 'store.goodsLabel/detail',
        method: 'GET',
        params
    })
}

export const createStoreGoodsLabelApi = (data: any) => {
    return request({
        url: 'store.goodsLabel/create',
        method: 'POST',
        data
    })
}
export const updateStoreGoodsLabelApi = (data: any) => {
    return request({
        url: `store.goodsLabel/update`,
        method: 'PUT',
        data
    })
}
export const updateStatusStoreGoodsLabelApi = (data: { id: number | string, status: number | string }) => {
    return request({
        url: `store.goodsLabel/status`,
        method: 'PUT',
        data
    })
}

export const deleteStoreGoodsLabelApi = (data: { ids: number[] | string[] }) => {
    return request({
        url: `store.goodsLabel/delete`,
        method: 'DELETE',
        data
    })
}