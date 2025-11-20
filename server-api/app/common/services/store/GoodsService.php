<?php
declare(strict_types=1);

namespace app\common\services\store;

use app\common\constants\StoreConstant;
use app\common\services\BaseService;
use app\common\dao\store\GoodsDao;
use app\common\services\common\CacheService;

/**
 * Class GoodsService
 */
class GoodsService extends BaseService
{
    public string $cacheKey = "store:goods:";

    public mixed $cacheService;
    /**
     * GoodsService constructor.
     * @param GoodsDao $dao
     */
    public function __construct(GoodsDao $dao)
    {
        $this->dao = $dao;
        $this->cacheService = app(CacheService::class);
    }


    /**
     * 删除商品详细缓存
     */
    public function deleteCache($goodsId = 0){
        if(!$goodsId){
            $this->cacheService->clear();
        }else{
            $this->cacheService->delete(StoreConstant::GOODS_CACHE_KEY.$goodsId);
        }
    }
}
