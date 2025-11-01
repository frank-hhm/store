import { request } from '@/utils/request/default'

export const getStoreShippingTemplatesListApi = (params:any) => {
    return request({
        url: 'store.shippingTemplates/list',
        method: 'GET',
        params
    })
}
export const getStoreShippingTemplatesSelectApi = () => {
    return request({
        url: 'store.shippingTemplates/select',
        method: 'GET'
    })
}

export const createStoreShippingTemplatesApi = (data:any) => {
    return request({
        url: 'store.shippingTemplates/create',
        method: 'POST',
        data
    })
}
export const updateStoreShippingTemplatesApi = (data:any) => {
    return request({
        url: `store.shippingTemplates/update`,
        method: 'PUT',
        data
    })
}
export const updateStatusStoreShippingTemplatesApi = (data:{id:number|string,status:number|string}) => {
    return request({
        url: `store.shippingTemplates/status`,
        method: 'PUT',
        data
    })
}

export const deleteStoreShippingTemplatesApi = (data:{id:number|string}) => {
    return request({
        url: `store.shippingTemplates/delete`,
        method: 'DELETE',
        data
    })
}

export const getStoreShippingTemplatesDetailApi = (params:{id:number|string}) => {
    return request({
        url: `store.shippingTemplates/detail`,
        method: 'GET',
        params
    })
}