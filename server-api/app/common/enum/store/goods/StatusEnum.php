<?php
declare(strict_types=1);

namespace app\common\enum\store\goods;


use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class StatusEnum
 * @package app\common\enum\store\goods
 */
class StatusEnum extends BaseEnum
{
    // 在售
    const NORMAL = 1;

    // 下架
    const DISABLE = 0;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::NORMAL => [
                'name' => '在售',
                'value' => self::NORMAL,
            ],
            self::DISABLE => [
                'name' => '下架',
                'value' => self::DISABLE,
            ],
        ];
    }
}

