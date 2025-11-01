<?php

/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2023/12/21 10:18
 */
declare(strict_types=1);

namespace app\sys\controller;
use app\sys\controller\Base;
use think\facade\{
    App
};
use app\common\services\RegionService;

/**
 * 地区数据
 * Class Region
 * @package app\sys\controller
 */
class Region extends Base
{
    /**
     * Region constructor.
     * @param App $app
     * @param RegionService $service
     */
    public function __construct(App $app, RegionService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 城市数据列表
     * @noAuth(true)
     * @method(GET)
     */
    public function list(){
        $pid = $this->request->param('pid') ?? 0;
        $this->success("获取成功",$this->service->getSubDataByPid($pid));
    }

    /**
     * 城市数据列表
     * @noAuth(true)
     * @method(GET)
     */
    public function treeList(){
        $this->success("获取成功",['tree'=>$this->service->getCacheTree(),'count'=>$this->service->getCacheCounts()]);
    }

    /**
     * 城市联级数据
     * @noAuth(true)
     * @method(GET)
     */
    public function getCascaderRegion(){
        $this->success("获取成功",$this->service->getCascaderRegion());
    }
}
