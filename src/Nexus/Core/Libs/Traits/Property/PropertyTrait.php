<?php

namespace Nexus\Core\Libs\Traits\Property;

use Nexus\Core\Enum\SymbolicCodeEnum;

Trait PropertyTrait
{
    public function __call(string $_methodName, array $_args):mixed
    {
        if(!preg_match('~^(set|get)([A-Z])(.*)$~', $_methodName, $_matches) && !method_exists($this, $_methodName))
            throw new \ErrorException('method : '. $_methodName . ' not exists');

        $_property = $this->_createProperty($_matches);

        switch($_matches[1]) {
            case 'set':
                $this->_checkArguments($_args, 1, 1, $_methodName);
                return $this->_set($_property, array_shift($_args));
            case 'get':
                $this->_checkArguments($_args, 0, 0, $_methodName);
                return $this->_get($_property);
            default:
                throw new \ErrorException(sprintf('Method Not Exists : %s ', $_methodName));
        }
    }

    private function _createProperty(array $_matches):string {

        $_property = sprintf('%s%s%s', SymbolicCodeEnum::UNDERBAR->value, strtolower($_matches[2]), $_matches[3]);
        if(!property_exists($this, $_property))
            throw new \ErrorException(sprintf('Property Not exists : %s %s', get_class($this), $_property));

        return $_property;
    }

    private function _checkArguments(array $_args, int $_min, int $_max, string $_methodName) :void {
        $_count = count($_args);

        if($_count < $_min || $_count > $_max)
            throw new \ErrorException(sprintf('MethodName: %s argment : %s agruments given', $_methodName, $_count));
        return;
    }

    private function _get(string $_property):mixed {
        return $this->$_property;
    }

    private function _set(string $_property, mixed $_value) :self {
        $this->$_property = $_value;
        return $this;
    }
}