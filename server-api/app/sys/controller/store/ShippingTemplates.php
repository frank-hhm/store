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
use app\sys\services\store\ShippingTemplatesService;

/**
 * 商城-运费模版
 * Class ShippingTemplates
 * @package app\sys\controller\store
 */
class ShippingTemplates extends Base
{
    /**
     * ShippingTemplates constructor.
     * @param App $app
     * @param ShippingTemplatesService $service
     */
    public function __construct(App $app,ShippingTemplatesService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 运费模版列表
     * @method(GET)
     */
    public function list()
    {
        $params = $this->request->getMore([
            ['name', ''],
        ]);
        $this->success("获取成功",$this->service->getShippingTemplatesList($params));
    }

    /**
     * 运费模版数据
     * @noAuth(true)
     * @method(GET)
     */
    public function select()
    {
        $this->success("获取成功",$this->service->getShippingTemplatesSelect());
    }

    /**
     * 运费模版详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        $this->success("获取成功",$this->service->getShippingTemplatesDetail($this->request->get('id')));
    }

    /**
     * 添加运费模版
     * @method(POST)
     */
    public function create()
    {
        $teml = $this->request->postMore([
            'name',
            ['is_free_shipping', 1],
            ['type', 1],
            ['sort', 0],
        ]);
        $citys = $this->request->param('citys');
        $free = $this->request->param('free_shipping_citys');
        if (!$teml['name']) $this->error('请输入模版名称！');
        if($this->service->createTemplate( $teml ,$citys,$free)){
            $this->success('添加成功!');
        }
        $this->error('添加失败!');
    }

    /**
     * 编辑运费模版
     * @method(PUT)
     */
    public function update()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $teml = $this->request->postMore([
            'name',
            ['is_free_shipping', 1],
            ['type', 1],
            ['sort', 0],
        ]);
        $citys = $this->request->param('citys');
        $free = $this->request->param('free_shipping_citys');
        if (!$teml['name']) $this->error('请输入模版名称！');
        if($this->service->updateTemplate($id, $teml ,$citys,$free)){
            $this->success('修改成功!');
        }
        $this->error('修改失败!');
    }

    /**
     * 删除运费模版
     * @method(DELETE)
     */
    public function delete()
    {
        if (!$ids = $this->request->param('ids')) {
            $this->error('参数错误!');
        }

        if ($this->service->deleteTemplate($ids)) {
            $this->success('删除成功!');
        }
        $this->error('删除失败!');
    }

}