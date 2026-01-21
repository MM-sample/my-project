<?php

namespace Nexus\Core\Environment;

class Ini
{
    public static function set(string $_item, mixed $_data):void {
        $_item = mb_strtolower($_item, mb_internal_encoding());
        if(!ini_set($_item, $_data))
            throw new \RuntimeException(sprintf('Failure Environment %s : %s ', $_item, $_data));
        return;
    }

    public static function setAll(array $_data):void {
        foreach($_data as $_item => $_val) {
            $_item = mb_strtolower($_item, mb_internal_encoding());
            if(!ini_set($_item, $_val))
                throw new \ErrorException(sprintf('Failure Environment  %s : %s', $_item, $_val));
        }
        return;
    }

    public static function get(string $_item):mixed {
        if(($_ret = ini_get($_item)) === false)
            throw new \RuntimeException(sprintf('Faild Get Ini Item : %s', $_item));

        return $_ret;
    }

}