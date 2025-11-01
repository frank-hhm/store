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
 * 商城-运费模版
 * Class ShippingTemplatesModel
 * @package app\common\model\store
 */
class ShippingTemplatesModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'store_shipping_templates';

    /**
     * 关联物流模板配送区域
     */
    public function citys()
    {
        return $this->hasMany(ShippingRegionsModel::class,'temp_id','id');
    }

    /**
     * 关联物流模板 免邮城市
     */
    public function freeCitys()
    {
        return $this->hasMany(ShippingFreeModel::class,'temp_id','id');
    }

    /**
     * 计费方式修改器
     */
    public function getTypeAttr($value)
    {
        return EnumFactory::instance('store.shipping_type')->getItem($value);
    }
}