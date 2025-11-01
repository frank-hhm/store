import { nextTick, ref } from "vue"
import store from "@/store"
import { defineStore } from "pinia"
import { getCacheRegions, setCacheRegions } from "@/utils"
import { RegionsType, Result } from "@/types"
import { getTreeListApi } from "@/api/region"
export const useRegionsStore = defineStore("Regions", () => {

    const regions = ref<RegionsType>(getCacheRegions() || [])

    const setRegions = (value: RegionsType) => {
        setCacheRegions(value)
        regions.value = value
    }


    /** 获取 */
    const initRegions = () => {
        getTreeListApi().then((res: Result) => {
            nextTick(() => {
                setRegions(res.data);
            })
        })
    }
    return { regions, setRegions, initRegions }
})

/** 在 setup 外使用 */
export function useRegionsStoreHook() {
    return useRegionsStore(store)
}


export default {
    useRegionsStore,
    useRegionsStoreHook
}