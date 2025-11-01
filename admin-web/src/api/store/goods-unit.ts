import { request } from '@/utils/request/default'

export const getStoreGoodsUnitListApi = (params: any) => {
    return request({
        url: 'store.goodsUnit/list',
        method: 'GET',
        params
    })
}

export const getStoreGoodsUnitSelectApi = () => {
    return request({
        url: 'store.goodsUnit/select',
        method: 'GET'
    })
}

export const createStoreGoodsUnitApi = (data: any) => {
    return request({
        url: 'store.goodsUnit/create',
        method: 'POST',
        data
    })
}
export const updateStoreGoodsUnitApi = (data: any) => {
    return request({
        url: `store.goodsUnit/update`,
        method: 'PUT',
        data
    })
}
export const deleteStoreGoodsUnitApi = (data: { ids: number[] | string[] }) => {
    return request({
        url: `store.goodsUnit/delete`,
        method: 'DELETE',
        data
    })
}

export const getStoreGoodsUnitDetailApi = (params: { id: number | string }) => {
    return request({
        url: `store.goodsUnit/detail`,
        method: 'GET',
        params
    })
}