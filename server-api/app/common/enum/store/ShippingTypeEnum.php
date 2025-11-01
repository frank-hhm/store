<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\enum\store;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class ShippingTypeEnum
 * @package app\common\enum\store\shipping
 */
class ShippingTypeEnum extends BaseEnum
{
    const PIECE = 1;

    const WEIGHT = 2;

    const VOLUME = 3;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::PIECE => [
                'name' => '按件',
                'unit' => '',
                'value' => self::PIECE,
            ],
            self::WEIGHT => [
                'name' => '按重量',
                'unit' => '重量(kg)',
                'value' => self::WEIGHT,
            ],
            self::VOLUME => [
                'name' => '按体积',
                'unit' => '体积(m³)',
                'value' => self::VOLUME,
            ],
        ];
    }
}
