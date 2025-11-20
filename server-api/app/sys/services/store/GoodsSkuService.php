<?php
declare(strict_types=1);

namespace app\sys\services\store;


use app\common\constants\StoreConstant;
use app\common\exception\CommonException;
use app\common\services\common\CacheService;

/**
 * Class GoodsSkuService
 * 缓存 file
 */
class GoodsSkuService extends \app\common\services\store\GoodsSkuService
{


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

    public function getAttrFormatKey($specData,int $goodsId): array
    {
        $cacheService = app(CacheService::class);
        $specKey = [];
        foreach ($specData as $key => $item) {
            $sku_attr = explode(':',$item['sku_attr']);
            foreach ($sku_attr as $k => $v) {
                $spec = $cacheService->get(StoreConstant::GOODS_SPEC_VALUES_CACHE_KEY.$v);
                if(!empty($spec)){
                    $specKey[] = $spec['spec'];
                }

            }
        }
        $attrKey = [];
        $GoodsSpecService = app(GoodsSpecService::class);
        $GoodsSpecValueService = app(GoodsSpecValueService::class);
        foreach (array_unique($specKey) as $v) {
            $specItem = $cacheService->get(StoreConstant::GOODS_SPEC_ITEMS_CACHE_KEY.$v);
            if(!empty($specItem)){
                $specRes = $GoodsSpecService->create([
                    'goods_id'=>$goodsId,
                    'name'=>$specItem['name'],
                ]);
                if (!$specRes) throw new CommonException('保存失败');
                $specId = (int)$specRes->id;
                foreach ($specItem['values'] as $value) {
                    $valueRes =$GoodsSpecValueService->create([
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
}
