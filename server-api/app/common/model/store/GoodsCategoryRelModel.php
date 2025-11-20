<?php

declare(strict_types=1);

namespace app\common\model\store;

use think\model\Pivot;

/**
 * 商品分类中间表
 * Class GoodsCategoryRelModel
 * @package app\common\model\store
 */
class GoodsCategoryRelModel extends Pivot
{
    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'store_goods_category_rel';
}
