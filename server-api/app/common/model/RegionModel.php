<?php

/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2023/12/21 10:14
 */
declare(strict_types=1);
namespace app\common\model;

use app\common\traits\ModelTrait;

/**
 * 地区
 * Class RegionModel
 * @package app\common\model
 */
class RegionModel extends BaseModel
{
    use ModelTrait;

    /**
     * 模型名称
     */
    protected $name = 'region';

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 类型自动转换
     * @var array
     */
    protected $type = [
        'id' => 'integer',
        'pid' => 'integer',
        'level' => 'integer',
    ];
}