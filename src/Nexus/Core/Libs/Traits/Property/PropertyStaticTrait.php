<?php

namespace Nexus\Core\Libs\Traits\Property;

use Nexus\Core\Enum\SymbolicCodeEnum;

Trait PropertyStaticTrait
{
    /**
     * function __callStatic
     *
     * @param string $_methodName
     * @param mixed $_args
     * @return mixed
     */
    public static function __callStatic(string $_methodName, mixed $_args):mixed {
        if(!preg_match('~^(set|get)([A-Z])(.*)$~', $_methodName, $_matches) && !method_exists(static::class, $_methodName))
            throw new \ErrorException('method : '. $_methodName . ' not exists');

        $_property = self::_createPropertyStatic($_matches);

        switch($_matches[1]) {
            case 'set':
                self::_checkArgumentsStatic($_args, 1, 1, $_methodName);
                return self::_setStatic($_property, current($_args));
            case 'get':
                self::_checkArgumentsStatic($_args, 0, 0, $_methodName);
                return self::_getStatic($_property);
            default:
                throw new \ErrorException(sprintf('Method Not Exists : %s ', $_methodName));
        }
    }

    private static function _createPropertyStatic(array $_matches):string {
        $_property = sprintf('%s%s%s', SymbolicCodeEnum::UNDERBAR->value, strtolower($_matches[2]), $_matches[3]);
        if(!property_exists(static::class, $_property))
            throw new \ErrorException(sprintf('Property Not exists : %s %s', static::class, $_property));

        return $_property;
    }

    private static function _checkArgumentsStatic(array $_args, int $_min, int $_max, string $_methodName):void {
        $_count = count($_args);
        if($_count < $_min || $_count > $_max)
            throw new \ErrorException(sprintf('MethodName: %s argment : %s agruments given', $_methodName, $_count));
        return;
    }

    private static function _getStatic(string $_property):mixed {
        return static::class::${$_property};
    }

    private static function _setStatic(string $_property, mixed $_value) :string {
        static::class::${$_property} = $_value;
        return static::class;
    }
}