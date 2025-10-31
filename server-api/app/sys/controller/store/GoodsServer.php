<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\sys\controller\store;
use app\BaseController;
use app\sys\services\store\GoodsServerService;
use think\facade\App;

class GoodsServer extends BaseController
{

    /**
     * GoodsServer constructor.
     */
    public function __construct(App $app, GoodsServerService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 商品服务列表
     * @method(GET)
     */
    public function list()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['server_name', ''],
        ]);
        $this->success("获取成功",$this->service->getGoodsServerList($where));
    }

    /**
     * 商品服务数据
     * @noAuth(true)
     * @method(GET)
     */
    public function select()
    {
        $this->success("获取成功",$this->service->getGoodsServerSelect());
    }

    /**
     * 商品服务详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        $this->success("获取成功",$this->service->getGoodsServerDetail($this->request->get('id')));
    }

    /**
     * 添加商品服务
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            'server_name',
            'desc',
            'icon',
            ['status', 1],
            ['sort', 0],
        ]);
        if (!$data['server_name']) $this->error('请输入服务名称！');
        if (!$data['desc']) $this->error('服务内容不能为空！');
        if (!$data['icon']) $this->error('服务图标不能为空！');
        if($this->service->save( $data)){
            $this->success('添加成功!');
        }
        $this->error('添加失败!');
    }

    /**
     * 编辑商品服务
     * @method(PUT)
     */
    public function update()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $data = $this->request->postMore([
            'server_name',
            'desc',
            'icon',
            ['status', 1],
            ['sort', 0],
        ]);
        if (!$data['server_name']) $this->error('请输入服务名称！');
        if (!$data['desc']) $this->error('服务内容不能为空！');
        if (!$data['icon']) $this->error('服务图标不能为空！');
        if($this->service->update($id, $data)){
            $this->success('修改成功!');
        }
        $this->error('修改失败!');
    }

    /**
     * 删除商品服务
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

    /**
     * 修改商品服务状态
     * @method(PUT)
     */
    public function status()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        if ($this->service->update($id, ['status' => $this->request->param('status')])) {
            $this->success('修改成功');
        }
        $this->error('修改失败');
    }
}
