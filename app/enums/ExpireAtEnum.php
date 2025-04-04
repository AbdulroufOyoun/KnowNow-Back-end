<?php

namespace App\Enums;

class ExpireAtEnum
{

    const firstClass = 3;
    const secondClass = 2;

    public static function toArray()
    {
        return [
            self::firstClass,
            self::secondClass,
        ];
    }
}
