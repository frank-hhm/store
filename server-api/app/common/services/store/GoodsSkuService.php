<?php
declare(strict_types=1);

namespace app\common\services\store;

use app\common\constants\StoreConstant;
use app\common\dao\store\GoodsSkuDao;
use app\common\exception\CommonException;
use app\common\helper\StringHelper;
use app\common\services\BaseService;
use app\common\services\common\CacheService;

/**
 * Class GoodsSkuService
 * 缓存 file
 */
class GoodsSkuService extends BaseService
{

    /**
     * GoodsSkuService constructor.
     * @param GoodsSkuDao $dao
     */
    public function __construct(GoodsSkuDao $dao)
    {
        $this->dao = $dao;
    }

    public function deleteSku($goodsId,$isDelSpec = true){
        if($isDelSpec){
            $this->deleteSkuSpec($goodsId);
        }
        return $this->dao->delete($goodsId,'goods_id');
    }

    public function deleteSkuSpec($goodsId){
        app(GoodsSpecService::class)->delete($goodsId,'goods_id');
        app(GoodsSpecValueService::class)->delete($goodsId,'goods_id');
        return $this->dao->delete($goodsId,'goods_id');
    }

    public function getSkuDetail($filter): array
    {
        $detail = $this->dao->detail($filter);
        if (!$detail) {
            throw new CommonException('规格sku不存在');
        }
        return $detail->toArray();
    }


    /**
     * 添加规格属性至缓存
     */
    public function createCacheSpec(array $specData): array
    {
        $cacheService = app(CacheService::class);
        $cacheTime = 3600 * 12;
        foreach ($specData as $key => &$item) {
            $specKey = StringHelper::uniqidNumber();
            foreach ($item['values'] as $k => &$v) {
                $specValueKey = StringHelper::uniqidNumber();
                $v['id'] = $specValueKey;
                $v['spec'] = $specKey;
                $cacheService->set(StoreConstant::GOODS_SPEC_VALUES_CACHE_KEY.$specValueKey,$v,$cacheTime);
            }
            $cacheService->set(StoreConstant::GOODS_SPEC_ITEMS_CACHE_KEY.$specKey,$item,$cacheTime);
            $item['id'] = $specKey;
        }
        return $this->attrFormat($specData);
    }

    /**
     * 格式化属性
     */
    public function attrFormat($arr): array
    {
        $len = count($arr);
        $names = array_column($arr, 'name');
        $attrs = [];
        if ($len > 0) {
            if ($len > 1) {
                $attrs = $arr[0]['values'];
                for ($i = 0; $i < $len - 1; $i++) {
                    $temp = $attrs;
                    $attrs = [];
                    foreach ($temp as $item) {
                        foreach ($arr[$i + 1]['values'] as $datum) {
                            $attrs[] = [
                                'name'=>trim($item['name']) . ',' . trim($datum['name']),
                                'image'=>($item['image'] ?? '') .',' . ($datum['image'] ?? ''),
                                'id'=>$item['id'] . ':' .$datum['id'],
                            ];
                        }
                    }
                }
            } else {
                foreach ($arr[0]['values'] as $item) {
                    $attrs[] = [
                        'name'=>$item['name'],
                        'image'=>$item['image'] ?? '',
                        'id'=>$item['id'],
                    ];
                }
            }
        }
        return ['attrs'=>$attrs,'names'=>$names];
    }


    /**
     * 库存预警商品
     */
    public function getGoodsStockWarning(): string
    {
        return number_format($this->dao->search(['goods_status'=>1])->where('stock', '<=', sysconf('store_stock'))->count());
    }
}
