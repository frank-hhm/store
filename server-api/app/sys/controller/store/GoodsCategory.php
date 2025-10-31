<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);
namespace app\sys\controller\store;

use think\facade\App;

use app\sys\controller\Base;
use app\sys\services\store\GoodsCategoryService;

/**
 * 商城商品分类
 * Class GoodsCategory
 * @package app\sys\controller\store
 */
class GoodsCategory extends Base
{

    /**
     * GoodsCategory constructor.
     */
    public function __construct(App $app, GoodsCategoryService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }


    /**
     * 商品分类列表
     * @method(GET)
     */
    public function list()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['category_name', ''],
        ]);
        $this->success("获取成功",$this->service->getGoodsCategoryList($where));
    }
    /**
     * 商品分类详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        $this->success("获取成功",$this->service->getGoodsCategoryDetail($this->request->get('id')));
    }

    /**
     * 添加商品分类
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            'category_name',
            'icon',
            'image',
            ['status', 1],
            ['sort', 0],
            ['pid', 0],
        ]);
        if (!$data['category_name']) $this->error('请输入分类名称！');
        if (!$data['icon']) $this->error('分类图标不能为空！');
        if (!$data['image']) $this->error('分类大图不能为空！');
        if($this->service->save($data)){
            $this->service->cache();
            $this->success('添加成功!');
        }
        $this->error('添加失败!');
    }

    /**
     * 编辑商品分类
     * @method(PUT)
     */
    public function update()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $data = $this->request->postMore([
            'category_name',
            'icon',
            'image',
            ['status', 1],
            ['sort', 0],
            ['pid', 0],
        ]);
        if (!$data['category_name']) $this->error('请输入分类名称！');
        if (!$data['icon']) $this->error('分类图标不能为空！');
        if (!$data['image']) $this->error('分类大图不能为空！');
        if($this->service->update($id, $data)){
            $this->service->cache();
            $this->success('修改成功!');
        }
        $this->error('修改失败!');
    }

    /**
     * 删除商品分类
     * @method(DELETE)
     */
    public function delete()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }

        if ($this->service->delete((int)$id)) {
            $this->service->cache();
            $this->success('删除成功!');
        }
        $this->error('删除失败!');
    }

    /**
     * 修改商品分类状态
     * @method(PUT)
     */
    public function status()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        if ($this->service->update($id, ['status' => $this->request->param('status')])) {
            $this->service->cache();
            $this->success('修改成功');
        }
        $this->error('修改失败');
    }

    /**
     * 获取商品分类Tree
     * @noAuth(true)
     * @method(GET)
     */
    public function getSelectTree(){
        $pid = $this->request->get('pid',0);
        $this->success("获取成功",$this->service->getSelectTree($pid));
    }

    /**
     * 商品分类select
     * @noAuth(true)
     * @method(GET)
     */
    public function getSelect()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['category_name', ''],
        ]);
        $this->success("获取成功",$this->service->getGoodsCategoryList($where,['id','category_name','pid']));
    }
}