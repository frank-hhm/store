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
 * 商城-商品服务
 * Class GoodsServerModel
 * @package app\common\model\store
 */
class GoodsServerModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'store_goods_server';

    /**
     * 状态获取器
     */
    public function getStatusAttr($value)
    {
        return EnumFactory::instance('status')->getItem($value);
    }

}