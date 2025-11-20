<?php

declare(strict_types=1);
namespace app\common\constants;

/**
 * @Constants
 */
class StoreConstant
{
    //商品缓存
    public const GOODS_CACHE_KEY = "store:goods:detail:";
    //商品SKU spec缓存
    public const GOODS_SPEC_ITEMS_CACHE_KEY = "store:goods:spec:items:";
    //商品SKU spec缓存
    public const GOODS_SPEC_VALUES_CACHE_KEY = "store:goods:spec:values:";
}