<?php

namespace Nexus\Core\Environment\Parts\Traits;

use Nexus\Core\Enum\SymbolicCodeEnum;

trait EnvTrait
{
    public function setIniKey(string $_targetKey):self {
        $this->_iniKey = $_targetKey;
        return $this;
    }

    public function init(string $_envKey, string $_envValue):self {
        return $this->_doDistribution($_envKey, $_envValue);
    }

    public function doAddContents(string $_envKey, string $_envValue):self {
        if(str_starts_with($_envKey, $this->_iniKey))
            return $this->_doSetIniInfo($_envKey, $_envValue);

        if($this->_isOptionDB($_envKey))
            return $this->_setOptionDB($_envKey, $_envValue);

        if(!array_key_exists($_envKey, $this->list)) {
            $this->list[$_envKey] = $this->_doConvertBool($_envValue);
            return $this;
        }

        if(!is_array($this->list[$_envKey])) {
            $this->list[$_envKey] = [$this->list[$_envKey], $this->_doConvertBool($_envValue)];
            return $this;
        }

        $this->list[$_envKey] = array_merge($this->list[$_envKey], [$this->_doConvertBool($_envValue)]);
        return $this;
    }

    private function _doDistribution(string $_envKey, string $_envValue):self {
        if(str_starts_with($_envKey, $this->_iniKey))
            return $this->_doSetIniInfo($_envKey, $_envValue);

        if($this->_isOptionDB($_envKey))
            return $this->_setOptionDB($_envKey, $_envValue);

        $this->list[$_envKey] = $this->_doConvertBool($_envValue);
        return $this;
    }

    private function _doSetIniInfo(string $_envKey, string $_envValue):self {
        $_envKey = str_replace(sprintf('%s%s', $this->_iniKey, SymbolicCodeEnum::DOT->value), '', $_envKey);
        $this->list[$this->_iniKey][$_envKey] = $_envValue;
        return $this;
    }

    private function _isOptionDB(string $_envKey):bool {
        foreach(self::DB_OPTION_TARGET_LIST as $_optionKey => $_val) {
            if(str_starts_with($_envKey, $_optionKey))
                return true;
        }
        return false;
    }

    private function _setOptionDB(string $_envKey, string $_envValue):self {
        foreach(self::DB_OPTION_TARGET_LIST as $_optionKey => $_val) {
            if(!str_starts_with($_envKey, $_optionKey))
                continue;

            $this->list[$_val][$_envKey] = $this->_doConvertBool($_envValue);
        }
        return $this;
    }

    private function _doConvertBool(mixed $_value):mixed {
        return match($_value) {
            'true'  => true,
            'false' => false,
            default => $_value
        };
    }

}