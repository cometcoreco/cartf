<?php

namespace App\Enums;

use ReflectionClass;

class LandlordCouponType
{
    const Percentage = 0;
    const Amount = 1;

    public static function getText(int $const)
    {
        foreach (self::getCouponTypeList() as $index => $item) {
            if ($const == $index) {
                return (strtolower($item));
            }
        }

        return [];
    }

    public static function getCouponTypeList(): array
    {
        $valueArr = [];
        foreach (self::getAttributes() as $index => $attribute) {
            $valueArr[$attribute] = __(ucwords(strtolower($index)));
        }

        return $valueArr;
    }

    public static function getCouponTypeValues(): array
    {
        $valueArr = [];
        foreach (self::getAttributes() as $attribute) {
            $valueArr[] = $attribute;
        }

        return $valueArr;
    }

    private static function getAttributes(): array
    {
        $reflect = new ReflectionClass(__CLASS__);
        return $reflect->getConstants() ?? [];
    }
}
