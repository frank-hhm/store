import { request } from '@/utils/request/default'

export const getStoreGoodsCategoryListApi = (params:any) => {
    return request({
        url: 'store.goodsCategory/list',
        method: 'GET',
        params
    })
}

export const createStoreGoodsCategoryApi = (data:any) => {
    return request({
        url: 'store.goodsCategory/create',
        method: 'POST',
        data
    })
}
export const updateStoreGoodsCategoryApi = (data:any) => {
    return request({
        url: `store.goodsCategory/update`,
        method: 'PUT',
        data
    })
}
export const updateStatusStoreGoodsCategoryApi = (data:{id:number|string,status:number|string}) => {
    return request({
        url: `store.goodsCategory/status`,
        method: 'PUT',
        data
    })
}

export const deleteStoreGoodsCategoryApi = (data:{id:number|string}) => {
    return request({
        url: `store.goodsCategory/delete`,
        method: 'DELETE',
        data
    })
}


export const getStoreGoodsCategorySelectTreeApi = (params:any) => {
    return request({
        url: 'store.goodsCategory/getSelectTree',
        method: 'GET',
        params
    })
}
export const getStoreGoodsCategorySelectApi = (params:any) => {
    return request({
        url: 'store.goodsCategory/getSelect',
        method: 'GET',
        params
    })
}

export const getStoreGoodsCategoryDetailApi = (params:{id:number|string}) => {
    return request({
        url: `store.goodsCategory/detail`,
        method: 'GET',
        params
    })
}

