<?php
declare(strict_types=1);

namespace app\common\services\store;

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
        GoodsSpecService::instance()->delete($goodsId,'goods_id');
        GoodsSpecValueService::instance()->delete($goodsId,'goods_id');
        return $this->dao->delete($goodsId,'goods_id');
    }

    public function getSkuDetail($filter)
    {
        $detail = $this->dao->detail($filter);
        if (!$detail) {
            throw new CommonException('规格sku不存在');
        }
        return $detail->toArray();
    }


    public function saveSku(array $specData,$specType,int $goodsId){
        $skuList = [];
        $isCreate = true;
        foreach ($specData as $key => &$item) {
            $sku = [
                "goods_id" => $goodsId,
                "goods_image" => $item['goods_image'] ?? "",
                "price" => $item['price'] ?? 0.00,
                "cost_price" => $item['cost_price'] ?? 0.00,
                "market_price" => $item['market_price']?? 0.00,
                "stock" => $item['stock'] ?? 0,
                "bar_code" => $item['bar_code'] ?? '',
                "weight" => $item['weight'] ?? '',
                "volume" => $item['volume'] ?? '',
                "sku_attr" => explode(':',$item['sku_attr'] ?? ''),
                "sku_attr_text" => $item['sku_attr_text'] ?? '',
            ];
            if(!empty($item['id'])){
                $isCreate = false;
                $sku['id'] = $item['id'];
            }
            $skuList[] = $sku;
        }

        $isCreate && $this->deleteSku($goodsId);

        if($specType == 2 && $isCreate){
            $specCacheKey = $this->getAttrFormatKey($specData,$goodsId);
            //删除关联的活动sku

        }
        foreach ($skuList as $key => &$item) {
            foreach ($item['sku_attr'] as $k => $v) {
                if(!empty($specCacheKey[$v]) && $isCreate){
                    $item['sku_attr'][$k] = $specCacheKey[$v];
                }
            }
            $item['sku_attr'] = implode(':', $item['sku_attr']);
            if(!$isCreate){
                $this->dao->update($item['id'],$item,'id');
            }
        }
        return $isCreate ? $this->dao->saveAll($skuList) : true;
    }

    /**
     * 添加规格属性至缓存
     */
    public function createCacheSpec(array $specData): array
    {
        $cacheService = CacheService::instance();
        $cacheTime = 3600 * 12;
        foreach ($specData as $key => &$item) {
            $specKey = StringHelper::uniqidNumber();
            foreach ($item['values'] as $k => &$v) {
                $specValueKey = StringHelper::uniqidNumber();
                $v['id'] = $specValueKey;
                $v['spec'] = $specKey;
                $cacheService->set('spec:values:'.$specValueKey,$v,$cacheTime);
            }
            $cacheService->set('spec:items:'.$specKey,$item,$cacheTime);
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

    public function getAttrFormatKey($specData,int $goodsId): array
    {
        $cacheService = CacheService::instance();
        $specKey = [];
        foreach ($specData as $key => $item) {
            $sku_attr = explode(':',$item['sku_attr']);
            foreach ($sku_attr as $k => $v) {
                $spec = $cacheService->get('spec:values:'.$v);
                if(!empty($spec)){
                    $specKey[] = $spec['spec'];
                }

            }
        }
        $attrKey = [];
        foreach (array_unique($specKey) as $v) {
            $specItem = $cacheService->get('spec:items:'.$v);
            if(!empty($specItem)){
                $specRes = GoodsSpecService::instance()->create([
                    'goods_id'=>$goodsId,
                    'name'=>$specItem['name'],
                ]);
                if (!$specRes) throw new CommonException('保存失败');
                $specId = (int)$specRes->id;
                foreach ($specItem['values'] as $value) {
                    $valueRes = GoodsSpecValueService::instance()->create([
                        'goods_id'=>$goodsId,
                        'spec_id'=>$specId,
                        'name'=>$value['name'],
                        'image'=>$value['image'] ?? '',
                    ]);
                    if (!$valueRes) throw new CommonException('保存失败');
                    $attrKey[$value['id']] = $valueRes->id;

                }
                $attrKey[$v] = $specId;
            }

        }
        return $attrKey;
    }



    /**
     * 库存预警商品
     */
    public function getGoodsStockWarning(): string
    {
        return number_format($this->dao->search(['goods_status'=>1])->where('stock', '<=', sysconf('store_stock'))->count());
    }
}
