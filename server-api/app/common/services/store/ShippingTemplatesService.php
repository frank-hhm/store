<?php

/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\services\store;

use app\common\dao\store\ShippingTemplatesDao;
use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\services\BaseService;
use app\common\services\store\ShippingFreeService;
use app\common\services\store\ShippingRegionsService;
use app\common\services\RegionService;

/**
 * Class ShippingTemplatesService
 */
class ShippingTemplatesService extends BaseService
{

    /**
     * ShippingTemplatesService constructor.
     * @param ShippingTemplatesDao $dao
     */
    public function __construct(ShippingTemplatesDao $dao)
    {
        $this->dao = $dao;
    }

    public function getShippingTemplatesDetail($filter): array
    {
        $detail = $this->dao->getDetail($filter);
        if (!$detail) {
            throw new CommonException('数据不存在');
        }
        $detail['citys'] = $this->getRegionData($detail['citys']);
        $detail['free_citys'] = $this->getRegionData($detail['free_citys']);
        return $detail->toArray();
    }

    /**
     * 获取服务列表
     */
    public function getShippingTemplatesList(array $params = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if (!empty($params["name"])){
            $filter[] = ["name","like","%".$params["name"]."%"];
        }

        $list = $this->dao->getShippingTemplatesList($filter, $page, $limit);

        foreach ($list['data'] as &$item){
            $item['citys'] = $this->getRegionData($item['citys']);
            $item['free_citys'] = $this->getRegionData($item['free_citys']);
        }
        return $list;
    }

    /**
     * 获取服务数据
     */
    public function getShippingTemplatesSelect(array $params = []): array
    {
        return $this->dao->getShippingTemplatesSelect($params);
    }

    public function createTemplate(array $data,array $citys,array $freeData){
        $regionsService = ShippingRegionsService::instance();
        $freeService = ShippingFreeService::instance();
        // 保存数据
        return $this->transaction(function () use ($data, $citys,$regionsService,$freeData,$freeService) {
            $res = $this->dao->create($data);
            if (!$res) throw new CommonException('保存失败');
            $id = (int)$res->id;
            $regionsService->createExpressRegions($citys,$id);
            $freeService->createFreeRegions($freeData,$id);
            return true;
        });
    }

    public function updateTemplate(int $id,array $data,array $citys,array $freeData){
        $regionsService = ShippingRegionsService::instance();
        $freeService = ShippingFreeService::instance();
        // 保存数据
        return $this->transaction(function () use ($id,$data, $citys,$regionsService,$freeData,$freeService) {
            $res = $this->dao->update($id,$data);
            if (!$res) throw new CommonException('保存失败');
            $regionsService->createExpressRegions($citys,$id);
            $freeService->createFreeRegions($freeData,$id);
            return true;
        });
    }
    public function deleteTemplate(array $ids){
        $regionsService = ShippingRegionsService::instance();
        $freeService = ShippingFreeService::instance();
        // 保存数据
        return $this->transaction(function () use ($ids,$regionsService,$freeService) {
            $res = $this->dao->delete($ids);
            if (!$res) throw new CommonException('删除失败');
            $regionsService->deleteExpressRegions($ids);
            $freeService->deleteFreeRegions($ids);
            return true;
        });
    }


    /**
     * 获取配送区域及运费设置项
     */
    public function getRegionData($list): array
    {
        // 所有地区
        $regions = RegionService::instance()->getCacheAll();
        $data = [];
        foreach ($list as $item) {
            $arr = $item;
            $arr['citys'] = ArrayHelper::objectToArray($item['citys']);
            $province = [];
            foreach ($arr['citys'] as $cityId) {
                if (!isset($regions[$cityId])) continue;
                !in_array($regions[$cityId]['pid'], $province) && $province[] = $regions[$cityId]['pid'];
            }
            $arr['province'] = $province;
            $data[] = $arr;
        }
        return $data;
    }


}