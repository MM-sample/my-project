<?php

namespace Nexus\Core\Enum\Traits;

Trait EnumTrait
{
    public static function findByValue(string|int $_value):?static {
        foreach(static::cases() as $_enumCase) {
            if($_enumCase->value !== $_value)
                continue;
            return $_enumCase;
        }
        return null;
    }

    public static function findByName(string $_name):?static {
        foreach(static::cases() as $_enumCase) {
            if($_enumCase->name !== $_name)
                continue;
            return $_enumCase;
        }
        return null;
    }
}
