<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\services\store;

use app\common\dao\store\GoodsSpecDao;
use app\common\services\BaseService;

/**
 * Class GoodsSpecService
 */
class GoodsSpecService extends BaseService
{

    /**
     * GoodsSpecService constructor.
     * @param GoodsSpecDao $dao
     */
    public function __construct(GoodsSpecDao $dao)
    {
        $this->dao = $dao;
    }

    public function getGoodsSpecData($goodsId): array
    {
        $select = $this->dao->where(['goods_id'=>$goodsId])->with(['values'])->select();
        return $select->toArray();
    }

}
