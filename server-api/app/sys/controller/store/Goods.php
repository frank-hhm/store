<?php

declare(strict_types=1);
namespace app\sys\controller\store;

use app\sys\controller\Base;
use think\facade\{
    App
};
use app\sys\services\store\GoodsService;
use app\sys\services\store\GoodsSkuService;

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
            ['category_id', ''],
            'destroy'
        ]);
        $this->success("获取成功",$this->service->getGoodsList($param));
    }

    /**
     * 商品详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $this->success("获取成功",$this->service->getGoodsFormDetail($id));
    }

    /**
     * 添加商品
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['category_id', []],
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


    /**
     * 编辑商品
     * @method(PUT)
     */
    public function update()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $data = $this->request->postMore([
            ['category_id', []],
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
        if($this->service->updateGoods((int)$id,$data ,$spec)){
            $this->success('修改成功!');
        }
        $this->error('修改失败!');
    }

    /**
     * 生成规格
     * @noAuth(true)
     * @method(POST)
     */
    public function createSpec(){

        $data = $this->request->postMore([
            ['spec', []],
        ]);
        if (!$data['spec']) $this->error('规格不能为空！');
        if($data = app(GoodsSkuService::class)->createCacheSpec( $data['spec'] )){
            $this->success('生成成功!',$data);
        }
        $this->error('生成失败!');
    }

}