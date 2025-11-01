<?php

/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2023/12/21 10:16
 */
declare(strict_types=1);

namespace app\common\services;

use app\common\dao\RegionDao;
use app\common\helper\ArrayHelper;
use app\common\services\common\CacheService;
use think\facade\Log;


/**
 * Class RegionService
 */
class RegionService extends BaseService
{

    // 当前数据版本号
    private string $version = '1.0';


    /**
     * RegionService constructor.
     * @param RegionDao $dao
     */
    public function __construct(RegionDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 根据id获取地区名称
     */
    public function getNameById($id)
    {
        $cacheKey = "region:list:name:";
        if( $id > 0){
            $cache = CacheService::instance()->get($cacheKey.$id);
            if(empty($cache)){
                $cache = $this->getCacheAll()[$id];
                CacheService::instance()->set($cacheKey.$id,$cache);
            }
            return $cache['name'];
        }
        return  '其他';
    }

    /**
     * 根据名称获取地区id
     */
    public function getIdByName($name, $level = 0, $pid = 0)
    {
        $cacheKey = "region:list:id:";

        $cache = CacheService::instance()->get($cacheKey.$name);
        if(empty($cache)){
            $data =$this->getCacheAll();
            foreach ($data as $item) {
                if (($item['name'] == $name || $item['shortname'] == $name) && $item['level'] == $level && $item['pid'] == $pid){
                    $cache = $item;
                    break;
                }
            }
            CacheService::instance()->set($cacheKey.$name,$cache);
        }
        return $cache['id'] ?? 0;
    }

    /**
     * 获取所有地区(树状结构)
     */
    public function getCacheTree()
    {
        return $this->getCacheData('tree');
    }

    /**
     * 获取所有地区列表
     * @return mixed
     */
    public function getCacheAll(): mixed
    {
        return  $this->getCacheData('all');
    }

    /**
     * 获取所有地区的总数
     */
    public function getCacheCounts()
    {
        return $this->getCacheData('count');
    }

    /**
     * 获取缓存中的数据(存入静态变量)
     */
    private function getCacheData($item = null)
    {
        $cacheData = $this->regionCache();
        if (is_null($item)) {
            return $cacheData;
        }
        return $cacheData[$item];
    }

    /**
     * 获取地区缓存
     * @throws \Exception
     */
    private function regionCache()
    {
        $cacheKey = 'region:all';
        $lockKey  = 'lock:region:all';
        $cacheService = app(CacheService::class);
        // 缓存的数据
        $complete = $cacheService->get($cacheKey);
        if(empty($complete)){
            $lock = $cacheService->redisHandler()->set($lockKey, 1, [
                'nx', // only set if not exists
                'ex' => 10 // 自动过期，防止死锁
            ]);
            $complete = [
                'all' => [],
                'tree' => [],
                'count' => 0,
                'version' => $this->version,
            ];
            if ($lock) {
                try {
                    // 获取锁成功，查询数据库
                    // 条件1: 获取缓存数据
                    // 条件2: 数据版本号要与当前一致
                    // 所有地区
                    $allList = $this->getAllList();

                    if ($allList) {
                        // 已完成的数据
                        $complete = [
                            'all' => $allList,
                            'tree' => $this->getTreeList($allList),
                            'count' => $this->getCount($allList),
                            'version' => $this->version,
                        ];
                        // 写入缓存
                        $cacheService->set($cacheKey, $complete, 3600 * 24);
                    } else {
                        // 防止缓存穿透，可写入空值或默认值
                        $cacheService->set($cacheKey, $complete, 60); // 短期缓存空结果
                    }

                    return $complete;
                } catch (\Exception $e) {
                    Log::error('Get region failed: ' . $e->getMessage());
                    throw $e;
                } finally {
                    // 释放锁
                    $cacheService->redisHandler()->del($lockKey);
                }
            } else {
                // 获取锁失败，说明其他进程正在重建缓存
                // 可以短暂休眠后重试，或直接返回空/默认值
                usleep(200000); // 等待 200ms
                return $cacheService->get($cacheKey) ?: [
                    'all' => [],
                    'tree' => [],
                    'count' =>0,
                    'version' => $this->version,
                ]; // 再次尝试从缓存读
            }
        }
        return $complete;
    }

    private function getCount($allList): array
    {
        $counts = [
            'total' => count($allList),
            'province' => 0,
            'city' => 0,
            'region' => 0,
        ];
        $level = [1 => 'province', 2 => 'city', 3 => 'region'];
        foreach ($allList as $item) {
            $counts[$level[$item['level']]]++;
        }
        return $counts;
    }

    /**
     * 格式化为树状格式
     */
    private function getTreeList($allList): array
    {
        $treeList = [];
        foreach ($allList as $pKey => $province) {
            if ($province['level'] == 1) {    // 省份
                $treeList[$province['id']] = $province;
                unset($allList[$pKey]);
                foreach ($allList as $cKey => $city) {
                    if ($city['level'] == 2 && $city['pid'] == $province['id']) {    // 城市
                        $treeList[$province['id']]['city'][$city['id']] = $city;
                        unset($allList[$cKey]);
                        foreach ($allList as $rKey => $region) {
                            if ($region['level'] == 3 && $region['pid'] == $city['id']) {    // 地区
                                $treeList[$province['id']]['city'][$city['id']]['region'][$region['id']] = $region;
                                unset($allList[$rKey]);
                            }
                        }
                    }
                }
            }
        }
        return $treeList;
    }

    /**
     * 从数据库中获取所有地区
     * @return array
     */
    private function getAllList(): array
    {
        $list = $this->dao->selectList([])->toArray();
        return ArrayHelper::arrayColumn2Key($list, 'id');
    }


    /**
     * 获取行政区
     */
    public function getRegionDataByCityId($cityId): array
    {
        $all = $this->getCacheAll();
        $data = [];
        foreach ($all as $key => $value) {
            if($value['pid'] == $cityId){
                $data[] = $value;
            }
        }
        return $data;
    }
    public function getSubDataByPid($Pid,$all = false): array
    {
        !$all && $all = $this->getCacheAll();
        $data = [];
        foreach ($all as $key => $value) {
            if($value['pid'] == $Pid){
                $value['hasChildren'] = count($this->getSubDataByPid($value['id'],$all)) > 0;
                $data[] = $value;
            }
        }
        return $data;
    }

    /**
     * 获取级联
     */
    public function getCascaderRegion($pid = 0): array
    {
        $list =  $this->getCacheAll();
        return ArrayHelper::getArrayTreeChildren($list, 'children');
    }
}
