<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\model\store;

use app\common\model\BaseModel;
use app\common\traits\ModelTrait;

/**
 * 商品规格属性
 * Class GoodsSpecValueModel
 * @package app\common\model\store
 */
class GoodsSpecValueModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'store_goods_spec_value';
}


