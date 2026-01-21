<?php

namespace Nexus\Core\Enum\Interface;

interface EnumInterface
{
    public static function findByValue(int $_value):?static;

    public static function findByName(string $_name):?static;

}