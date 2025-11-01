<?php

/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);


namespace app\sys\controller\store;

use app\sys\controller\Base;
use think\facade\{
    App
};
use app\common\services\store\GoodsSpecTemplatesService;
/**
 * 商城-商品规格模版
 * Class GoodsSpecTemplates
 * @package app\sys\controller\store
 */
class GoodsSpecTemplates extends Base
{
    /**
     * GoodsSpecTemplates constructor.
     * @param App $app
     * @param GoodsSpecTemplatesService $service
     */
    public function __construct(App $app,GoodsSpecTemplatesService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 商品规格模详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        $this->success("获取成功",$this->service->getGoodsSpecTemplatesDetail($this->request->get('id')));
    }


    /**
     * 商品规格模版列表
     * @method(GET)
     */
    public function list()
    {
        $where = $this->request->getMore([
        ]);
        $this->success("获取成功",$this->service->getGoodsSpecTemplatesList($where));
    }

    /**
     * 商品规格模版数据
     * @noAuth(true)
     * @method(GET)
     */
    public function select()
    {
        $this->success("获取成功",$this->service->getGoodsSpecTemplatesSelect());
    }

    /**
     * 添加商品规格模版
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['spec_temp_name',''],
            ['attrs',[]]
        ]);
        if (!$data['spec_temp_name']) $this->error('请输入规格模版名！');
        if($this->service->save( $data)){
            $this->success('添加成功!');
        }
        $this->error('添加失败!');
    }


    /**
     * 编辑商品规格模版
     * @method(PUT)
     */
    public function update()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $data = $this->request->postMore([
            ['spec_temp_name',''],
            ['attrs',[]]
        ]);
        if (!$data['spec_temp_name']) $this->error('请输入规格模版名！');
        if($this->service->update($id, $data)){
            $this->success('修改成功!');
        }
        $this->error('修改失败!');
    }


    /**
     * 删除商品规格模版
     * @method(DELETE)
     */
    public function delete()
    {
        if (!$ids = $this->request->param('ids')) {
            $this->error('参数错误!');
        }

        if ($this->service->delete([["id","in",$ids]])) {
            $this->success('删除成功!');
        }
        $this->error('删除失败!');
    }

}
