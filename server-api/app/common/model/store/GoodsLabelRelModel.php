<?php

declare(strict_types=1);

namespace app\common\model\store;

use think\model\Pivot;

/**
 * 商品标签中间表
 * Class GoodsLabelRelModel
 * @package app\common\model\store
 */
class GoodsLabelRelModel extends Pivot
{
    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'store_goods_label_rel';
}