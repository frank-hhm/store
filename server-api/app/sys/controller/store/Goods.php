<?php

declare(strict_types=1);
namespace app\sys\controller\store;

use app\sys\controller\Base;
use think\facade\{
    App
};
use app\common\services\store\GoodsService;
use app\common\services\store\GoodsSkuService;
/**
 * 商城-商品
 * Class Goods
 * @package app\sys\controller\store
 */
class Goods extends Base
{
    /**
     * Goods constructor.
     * @param App $app
     * @param GoodsService $service
     */
    public function __construct(App $app, GoodsService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 商品列表(详细)
     * @method(GET)
     */
    public function list()
    {
        $param = $this->request->getMore([
            ['goods_name', ''],
            ['cate_id', ''],
            'destroy'
        ]);
        $this->success("获取成功",$this->service->getGoodsList($param));
    }

    /**
     * 添加商品
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['cate_id', []],
            ['goods_name', ''],
            ['goods_images',[]],
            ['goods_label',[]],
            ['goods_unit',0],
            ['spec_type',1],
            ['content',''],
            ['delivery_type',[]],
            ['delivery_shipping',1],
            ['delivery_shipping_price',''],
            ['delivery_shipping_temp_id',0],
            ['sales_initial',0],
            ['keywords',''],
            ['selling_point',''],
            ['sort', 0],
            ['goods_server',[]],
            ['buy_limit_status',0],
            ['buy_limit',0],
            ['buy_limit_type',0],
            ['buy_least_status',0],
            ['buy_least',0],
            ['buy_least_type',0],

        ]);
        if (!$data['goods_name']) $this->error('请输入商品名称！');
        $spec = $this->request->param('spec');
        if($this->service->createGoods( $data ,$spec)){
            $this->success('添加成功!');
        }
        $this->error('添加失败!');
    }
}