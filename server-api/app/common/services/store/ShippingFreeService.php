<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\services\store;

use app\common\dao\store\ShippingFreeDao;
use app\common\exception\CommonException;
use app\common\services\BaseService;

/**
 * Class ShippingFreeService
 */
class ShippingFreeService extends BaseService
{

    /**
     * ShippingFreeService constructor.
     * @param ShippingFreeDao $dao
     */
    public function __construct(ShippingFreeDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 添加免邮模板区域
     */
    public function createFreeRegions(array $regions,int $tempId): bool|\think\Collection
    {
        $this->dao->delete($tempId,'temp_id');
        $regions = $this->getRegionsListData($regions,$tempId);
        if(count($regions) == 0) return true;
        return $this->dao->saveAll($regions);
    }

    public function deleteFreeRegions(array $tempIds = [])
    {
        return $this->dao->delete([['temp_id','in',$tempIds]]);
    }

    public function getRegionsListData(array $regions,int $tempId): array
    {
        $data = [];
        foreach ($regions as $key => $item) {
            if (!empty($item['number']) && !is_numeric($item['number'])) {
                throw new CommonException('包邮数量参数不对');
            }else if (!empty($item['money']) && !is_numeric($item['money'])) {
                throw new CommonException('包邮金额参数不对');
            }
            $data[] = [
                'temp_id' => $tempId,
                'citys' => $item['citys'],
                'number' => $item['number'],
                'money' => $item['money']
            ];
        }
        return $data;
    }
}