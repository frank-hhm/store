<?php

declare(strict_types=1);

namespace app\common\enum\store;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class DeliveryShippingEnum
 * @package app\common\enum\store
 */
class DeliveryShippingEnum extends BaseEnum
{
    const FREE = 1;

    const LOCK = 2;

    const TEMP = 3;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::FREE => [
                'name' => '包邮',
                'value' => self::FREE,
            ],
            self::LOCK => [
                'name' => '固定邮费',
                'value' => self::LOCK,
            ],
            self::TEMP => [
                'name' => '运费模板',
                'value' => self::TEMP,
            ],
        ];
    }
}

