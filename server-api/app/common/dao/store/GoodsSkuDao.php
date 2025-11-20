<?php

declare(strict_types=1);

namespace app\common\dao\store;

use app\common\dao\BaseDao;
use app\common\model\store\GoodsSkuModel;

/**
 * Class GoodsSkuDao
 * @package app\common\dao\store
 */
class GoodsSkuDao extends BaseDao
{
    /**
     * 设置模型名
     */
    protected function setModel(): string
    {
        return GoodsSkuModel::class;
    }
}
