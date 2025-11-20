<?php
declare(strict_types=1);

namespace app\common\model\store;

use think\model\Pivot;

/**
 * 商品服务中间表
 * Class GoodsServerRelModel
 * @package app\common\model\store
 */
class GoodsServerRelModel extends Pivot
{
    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'store_goods_server_rel';
}
