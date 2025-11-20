<?php

declare(strict_types=1);

namespace app\common\model\store;
use app\common\model\BaseModel;
use app\common\traits\ModelTrait;

/**
 * 商城-商品sku
 * Class GoodsSkuModel
 * @package app\common\model\store
 */
class GoodsSkuModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'store_goods_sku';


    protected $type = [
        'market_price'=>'float',
        'cost_price'=>'float',
        'price'=>'float',
        'volume'=>'float',
        'weight'=>'float',
        'bar_code'=>'string'
    ];

    /**
     * 获取器
     */
    public function getSkuAttrTextAttr($value): string
    {
        return empty($value)?'默认':$value;
    }
}
