<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\sys\controller\store;
use app\BaseController;
use app\sys\services\store\GoodsUnitService;
use think\facade\App;

class GoodsUnit extends BaseController
{

    /**
     * GoodsUnit constructor.
     */
    public function __construct(App $app , GoodsUnitService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 商品单位列表
     * @method(GET)
     */
    public function list()
    {
        $where = $this->request->getMore([
        ]);
        $this->success("获取成功",$this->service->getGoodsUnitList($where));
    }


    /**
     * 商品单位数据
     * @noAuth(true)
     * @method(GET)
     */
    public function select()
    {
        $this->success("获取成功",$this->service->getGoodsUnitSelect());
    }

    /**
     * 商品单位详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        $this->success("获取成功",$this->service->getGoodsUnitDetail($this->request->get('id')));
    }


    /**
     * 添加商品单位
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            'unit_name',
        ]);
        if (!$data['unit_name']) $this->error('请输入单位名称！');
        if($this->service->save( $data)){
            $this->success('添加成功!');
        }
        $this->error('添加失败!');
    }

    /**
     * 编辑商品单位
     * @method(PUT)
     */
    public function update()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $data = $this->request->postMore([
            'unit_name',
        ]);
        if (!$data['unit_name']) $this->error('请输入单位名称！');
        if($this->service->update($id, $data)){
            $this->success('修改成功!');
        }
        $this->error('修改失败!');
    }

    /**
     * 删除商品单位
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
