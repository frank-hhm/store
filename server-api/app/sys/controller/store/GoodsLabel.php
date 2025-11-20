<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\sys\controller\store;
use app\BaseController;
use think\facade\App;

use app\sys\services\store\GoodsLabelService;
class GoodsLabel extends BaseController
{

    /**
     * GoodsLabel constructor.
     */
    public function __construct(App $app, GoodsLabelService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }


    /**
     * 商品标签列表
     * @method(GET)
     */
    public function list()
    {
        $param = $this->request->getMore([
            ["label_name",""]
        ]);
        $this->success("获取成功",$this->service->getGoodsLabelList($param));
    }

    /**
     * 商品标签数据
     * @noAuth(true)
     * @method(GET)
     */
    public function select()
    {
        $this->success("获取成功",$this->service->getGoodsLabelSelect());
    }

    /**
     * 添加商品标签
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            'label_name',
        ]);
        if (!$data['label_name']) $this->error('请输入标签名称！');
        if($this->service->save( $data)){
            $this->success('添加成功!');
        }
        $this->error('添加失败!');
    }


    /**
     * 编辑商品标签
     * @method(PUT)
     */
    public function update()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $data = $this->request->postMore([
            'label_name',
        ]);
        if (!$data['label_name']) $this->error('请输入标签名称！');
        if($this->service->update($id, $data)){
            $this->success('修改成功!');
        }
        $this->error('修改失败!');
    }

    /**
     * 删除商品标签
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
     * 商品标签详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        $this->success("获取成功",$this->service->getGoodsLabelDetail($this->request->get('id')));
    }

}