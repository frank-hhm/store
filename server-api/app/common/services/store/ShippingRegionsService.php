<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\services\store;

use app\common\dao\store\ShippingRegionsDao;
use app\common\exception\CommonException;
use app\common\services\BaseService;


/**
 * Class ShippingRegionsService
 */
class ShippingRegionsService extends BaseService
{

    /**
     * ShippingRegionsService constructor.
     * @param ShippingRegionsDao $dao
     */
    public function __construct(ShippingRegionsDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 添加模板区域及运费
     */
    public function createExpressRegions(array $citys,int $tempId): bool|\think\Collection
    {
        $this->dao->delete($tempId,'temp_id');
        $citys = $this->getcitysListData($citys,$tempId);
        if(count($citys) == 0) return true;
        return $this->dao->saveAll($citys);
    }

    public function deleteExpressRegions(array $tempIds)
    {
        return $this->dao->delete([["temp_id","in",$tempIds]]);
    }

    public function getcitysListData(array $citys,int $tempId): array
    {
        $data = [];
        foreach ($citys as $key => $item) {
            if (!empty($item['first']) && !is_numeric($item['first'])) {
                throw new CommonException('首件、首重参数不对');
            }
            if (!empty($item['first_money']) && !is_numeric($item['first_money'])) {
                throw new CommonException('运费 (元)参数不对');
            }
            if (!empty($item['add_number']) && !is_numeric($item['add_number'])) {
                throw new CommonException('续件、续重参数不对');
            }
            if (!empty($item['add_money']) && !is_numeric($item['add_money'])) {
                throw new CommonException('续件、续重金额不对');
            }
            $data[] = [
                'temp_id' => $tempId,
                'citys' => $item['citys'],
                'first' => $item['first'],
                'first_money' => $item['first_money'],
                'add_number' => $item['add_number'],
                'add_money' => $item['add_money']
            ];
        }
        return $data;
    }
}
