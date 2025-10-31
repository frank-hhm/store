<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\model\store;

use app\common\enum\EnumFactory;
use app\common\model\BaseModel;
use app\common\traits\ModelTrait;

/**
 * 商城商品标签
 * Class GoodsLabelModel
 * @package app\common\model\store
 */
class GoodsLabelModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'store_goods_label';
}