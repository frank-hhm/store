<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\services\store;

use app\common\dao\store\GoodsCategoryDao;
use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\services\BaseService;
use app\common\services\common\CacheService;

/**
 * Class GoodsCategoryService
 */
class GoodsCategoryService extends BaseService
{

    public string $cacheKey = "store:category";

    /**
     * GoodsCategoryService constructor.
     * @param GoodsCategoryDao $dao
     */
    public function __construct(GoodsCategoryDao $dao)
    {
        $this->dao = $dao;
    }

    public function getGoodsCategoryDetail($filter): array
    {
        $detail = $this->dao->detail($filter);
        if (!$detail) {
            throw new CommonException('数据不存在');
        }
        return $detail->toArray();
    }

    /**
     * 获取分类列表
     */
    public function getGoodsCategoryList(array $params = [], array $field = ['*']): array
    {
        $filter = [];
        if (!empty($params["status"])){
            $filter[] = ["status","=",$params["status"]];
        }
        if (!empty($params["category_name"])){
            $filter[] = ["category_name","like","%".$params["category_name"]."%"];
        }
        $data = $this->dao->getGoodsCategoryAll($filter,$field);
        return ArrayHelper::getArrayTreeChildren($data);
    }

    /**
     * 获取分类选择下拉树
     */
    public function getSelectTree($pid = 0): array
    {
        $menuList = $this->dao->getGoodsCategoryAll([], ['id', 'pid', 'category_name']);
        $list = ArrayHelper::sortListTier($menuList, 0, 'pid', 'id');
        $menus = [['value' => 0, 'label' => '顶级分类']];
        foreach ($list as $menu) {
            $arr = ['value' => $menu['id'], 'label' => $menu['html'] . $menu['category_name']];
            if($pid && $pid == $menu['id']){
                $arr['disabled'] = true;
            }
            $menus[] = $arr;
        }
        return $menus;
    }

    /**
     * 获取所有的子级
     */
    public function getIdsByPid(int $pid = 0): array
    {
        $list = $this->dao->getGoodsCategoryAll([], ['id', 'pid', 'category_name']);
        $ids = $this->getSubIdsByPid($pid,$list);
        $pid!= -1 && $ids[] = (int) $pid;
        return $ids;
    }


    public function getSubIdsByPid($tabId,$list): array
    {
        $ids = [];
        foreach ($list as $key => $value) {
            if( $value['pid'] == $tabId ){
                $ids[] = $value['id'];
                $ids = array_merge($ids,$this->getSubIdsByPid($value['id'],$list));
            }
        }
        return $ids;
    }

    /**
     * 所有分类缓存
     * @return mixed
     */
    public function getCacheAll(): mixed
    {
        $cacheService = app(CacheService::class);
        $cache = $cacheService->get($this->cacheKey);
        if (!$cache) {
            $data = $this->dao->search(['status'=>1])->order(['sort' => 'desc', 'create_time' => 'desc'])->select();
            $all = !empty($data) ? $data->toArray() : [];
            $tree = [];
            foreach ($all as $first) {
                if ($first['pid'] != 0) continue;
                $twoTree = [];
                foreach ($all as $two) {
                    if ($two['pid'] != $first['id']) continue;
                    $threeTree = [];
                    foreach ($all as $three)
                        $three['pid'] == $two['id']
                        && $threeTree[$three['id']] = $three;
                    !empty($threeTree) && $two['child'] = $threeTree;
                    $twoTree[$two['id']] = $two;
                }
                if (!empty($twoTree)) {
                    array_multisort(array_column($twoTree, 'sort'), SORT_ASC, $twoTree);
                    $first['child'] = $twoTree;
                }
                $tree[$first['id']] = $first;
            }
            $cacheService->set($this->cacheKey,compact('all', 'tree'));
        }
        return $cacheService->get($this->cacheKey);
    }
    /**
     * 获取所有分类
     */
    public function getAll()
    {
        return self::getCacheAll()['all'] ?? [];
    }

    /**
     * 清除分类缓存
     */
    public function cache(): ?bool
    {
        return app(CacheService::class)->delete($this->cacheKey);
    }
}