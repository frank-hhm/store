<?php

declare(strict_types=1);


namespace app\common\model\store;

use app\common\enum\EnumFactory;
use app\common\model\BaseModel;
use app\common\traits\ModelTrait;
use think\model\concern\SoftDelete;
/**
 * 商品
 * Class GoodsModel
 * @package app\common\model\store
 */
class GoodsModel extends BaseModel
{
    use ModelTrait;
    use SoftDelete;
    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'store_goods';

    protected $deleteTime = 'delete_time';

    protected $defaultSoftDelete = 0;

    // 追加字段
    protected $append = [
        'goods_image'
    ];

    /**
     * 关联商品分类中间模型
     */
    public function categoryRel(): \think\model\relation\HasMany
    {
        return $this->hasMany(GoodsCategoryRelModel::class,'goods_id','id');
    }

    /**
     * 关联商品分类
     */
    public function category(): \think\model\relation\BelongsToMany
    {
        return $this->belongsToMany(GoodsCategoryModel::class,GoodsCategoryRelModel::class,'category_id','goods_id');
    }

    /**
     * 关联商品标签中间模型
     */
    public function labelRel(): \think\model\relation\HasMany
    {
        return $this->hasMany(GoodsLabelRelModel::class,'goods_id','id');
    }

    /**
     * 关联商品标签
     */
    public function label(): \think\model\relation\BelongsToMany
    {
        return $this->belongsToMany(GoodsLabelModel::class,GoodsLabelRelModel::class,'label_id','goods_id');
    }
    /**
     * 关联商品服务中间模型
     */
    public function serverRel(): \think\model\relation\HasMany
    {
        return $this->hasMany(GoodsServerRelModel::class,'goods_id','id');
    }

    /**
     * 关联商品服务
     */
    public function server(): \think\model\relation\BelongsToMany
    {
        return $this->belongsToMany(GoodsServerModel::class,GoodsServerRelModel::class,'server_id','goods_id');
    }

    /**
     * 关联商品规格
     */
    public function sku(): \think\model\relation\HasMany
    {
        return $this->hasMany(GoodsSkuModel::class,'goods_id','id');
    }

    /**
     * 商品图片设置器
     */
    public function setGoodsImagesAttr($value): string
    {
        if(is_array($value)){
            return implode(',', $value);
        }
        return $value;
    }

    /**
     * 状态修改器
     */
    public function getGoodsStatusAttr($value)
    {
        return EnumFactory::instance('store.goods.status')->getItem($value);
    }

    /**
     * 规格类型修改器
     */
    public function getSpecTypeAttr($value)
    {
        return EnumFactory::instance('store.goods.spec_type')->getItem($value);
    }

    /**
     * 规格类型修改器
     */
    public function getDeliveryShippingAttr($value)
    {
        return EnumFactory::instance('store.delivery_shipping')->getItem($value);
    }


    /**
     * 商品图片追加器
     */
    public function getGoodsImageAttr($value,$data): string
    {
        if(!empty($data['goods_images']) && !is_array($data['goods_images'])){
            return explode(',', $data['goods_images'])[0];
        }
        return '';
    }

    /**
     * 商品销量追加器
     */
    public function getGoodsSalesAttr($value,$data){
        return ($data['sales_initial'] ?? 0) + ( $data['sales_actual'] ?? 0);
    }


    /**
     * 配送方式设置器
     */
    public function setDeliveryTypeAttr($value): string
    {
        if(is_array($value)){
            return implode(',', $value);
        }
        return $value;
    }

    /**
     * 商品图片获取器
     */
    public function getGoodsImagesAttr($value): array
    {
        if(!empty($value)){
            return explode(',', $value);
        }
        return $value;
    }

    /**
     * 配送方式获取器
     */
    public function getDeliveryTypeAttr($value): array
    {
        if(!empty($value)){
            return explode(',', $value);
        }
        return [];
    }
}