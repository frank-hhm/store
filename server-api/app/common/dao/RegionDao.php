<?php

/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2023/12/21 10:17
 */
declare(strict_types=1);

namespace app\common\dao;
use app\common\model\RegionModel;

/**
 * Class RegionDao
 * @package app\common\dao
 */
class RegionDao extends BaseDao
{
    /**
     * 设置模型名
     */
    protected function setModel(): string
    {
        return RegionModel::class;
    }
}

