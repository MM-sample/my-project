<?php

namespace Nexus\Core\Libs\Path;

use Nexus\Core\Enum\SymbolicCodeEnum;

class NamespaceManager
{
    public static function doCreateNamespace(string $_className, string $_deleteWord):string {
        return strstr((new \ReflectionClass($_className))->getNamespaceName(), $_deleteWord, true);
    }

    public static function getClassNameExclusionNamespace(string $_className):string {
        $_tmpList = explode(SymbolicCodeEnum::BACKSLASH->value, $_className);
        return $_tmpList[array_key_last($_tmpList)];
    }

    public static function getController(string $_path):string {
        return self::_creatNamespace($_path);
    }

    public static function getRules():string {
        return self::_creatNamespace(PathManager::getRules());
    }

    private static function _creatNamespace(string $_targetPath):string {
        $_tmpPath = str_replace(PathManager::getRoot(), '', $_targetPath);
        return str_replace(DIRECTORY_SEPARATOR, SymbolicCodeEnum::BACKSLASH->value, $_tmpPath);
    }

}