export type UnknownObject = Record<string | number, unknown>;

export type StringObject = Record<string, unknown> | string | unknown;


export interface Result {
    message?: string;
    data?: any;
    code?: number | string;
    [key: string]: unknown;
}


export interface ResultError {
    status?: number | string;
    message?: string;
    code?: number | string | unknown;
    data?: {
        message?: string;
        code?: number | string | unknown;
        status?: number | string;
    };
    [key: string]: unknown;
}

export interface PageLimitType {
    total: number;
    limit?: number;
    page: number;
}

export type EnumItemType = {
    name: string;
    value: number | string;
    [key: string]: unknown | any;
}

export type EnumType = Array<EnumItemType>

export type EnumListsType = Record<string, EnumType>


export type RegionsItemType = {
    code:string;
    first:string;
    id:number;
    lat:string;
    level:number;
    lng:string;
    merger_name:string;
    name:string;
    pid:number;
    pinyin:string;
    shortname:string;
    zip_code:string;
    city: RegionsItemType[] | null
}

export interface RegionsType {
    count: {
        city: number;
        province: number;
        region:number;
        total:number;
    };
    tree: RegionsItemType[];
}

