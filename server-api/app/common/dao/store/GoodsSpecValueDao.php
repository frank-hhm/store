<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\dao\store;

use app\common\dao\BaseDao;
use app\common\model\store\GoodsSpecValueModel;

/**
 * Class GoodsSpecValueDao
 * @package app\common\dao\store
 */
class GoodsSpecValueDao extends BaseDao
{
    /**
     * 设置模型名
     */
    protected function setModel(): string
    {
        return GoodsSpecValueModel::class;
    }
}


