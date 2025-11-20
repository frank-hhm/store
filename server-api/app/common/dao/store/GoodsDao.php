<?php

declare(strict_types=1);

namespace app\common\dao\store;

use app\common\dao\BaseDao;
use app\common\model\store\GoodsModel;

/**
 * Class GoodsDao
 * @package app\common\dao\store
 */
class GoodsDao extends BaseDao
{
    /**
     * 设置模型名
     */
    protected function setModel(): string
    {
        return GoodsModel::class;
    }
}