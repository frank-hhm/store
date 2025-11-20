<?php

declare(strict_types=1);

namespace app\common\enum\store\goods;


use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class SpecTypeEnum
 * @package app\common\enum\store\goods
 */
class SpecTypeEnum extends BaseEnum
{
    const ONLY = 1;

    const MANY = 2;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::ONLY => [
                'name' => '单规格',
                'value' => self::ONLY,
            ],
            self::MANY => [
                'name' => '多规格',
                'value' => self::MANY,
            ],
        ];
    }
}
