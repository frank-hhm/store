import { request, requestProgress } from '@/utils/request/default'

export const getRegionListApi = (params: { pid: number }) => {
    return request({
        url: 'region/list',
        method: 'GET',
        params
    })
}

export const getTreeListApi = () => {
    return request({
        url: 'region/treeList',
        method: 'GET'
    })
}

export const getCascaderRegionApi = () => {
    return request({
        url: 'region/getCascaderRegion',
        method: 'GET'
    })
}

