<?php
namespace Nexus\Core\Environment;

use Nexus\Core\Enum\SymbolicCodeEnum;
use Nexus\Core\Environment\Parts\Traits\EnvTrait;
use Nexus\Core\Libs\Cache\FileCache;
use Nexus\Core\Libs\FileManager;
use Nexus\Core\Libs\Path\PathManager;

class Env
{
    private const INI_KEY = 'PHP_INI';
    private const DEFAULT_ENV_APP = 'local';
    private const EXPLODE_LIMIT = 2;

    private bool $_isCache = false;
    private array $_contents = [];
    private static ?Env $_instance = null;

    private function __construct(){}

    public static function init():void {
        if(is_null(self::$_instance))
            self::$_instance = new self();

        self::$_instance->_setIsCache();
        if (self::$_instance->_isCache) {
            self::$_instance->_doCacheContents();
            return;
        }
        self::$_instance->_contents = self::$_instance->_load(self::$_instance->_loadEnvFile());
    }

    public static function get(string $_hashKey, mixed $_default=null):mixed {
        return self::$_instance->_contents[$_hashKey] ?? $_default;
    }

    public static function getIni(?string $_hashKey=null, mixed $_default=null):mixed {
        if(is_null($_hashKey))
            return self::$_instance->_getAll(self::INI_KEY);

        return self::$_instance->_getOne(self::INI_KEY, $_hashKey, $_default);
    }

    public static function getOptionPDO(?string $_hashKey=null, mixed $_default=null):mixed {
        if(is_null($_hashKey))
            return self::$_instance->_getAll('PDO');

        return self::$_instance->_getOne('PDO', $_hashKey, $_default);
    }

    public static function getOptionDB(string $_db, ?string $_hashKey = null, mixed $_default = null):mixed {
        $_db = mb_strtoupper($_db);
        if(is_null($_hashKey))
            return self::$_instance->_getAll($_db);

        return self::$_instance->_getOne($_db, $_hashKey, $_default);
    }

    private function _getOne(string $_baseKey, string $_hashKey, mixed $_default):mixed {
        return  self::$_instance->_contents[$_baseKey][$_hashKey]?? $_default;
    }

    private function _getAll(string $_hashKey) :array {
        return self::$_instance->_contents[$_hashKey]?? [];
    }

    private function _setIsCache():void {
        if(($_appEnv = $_SERVER['APP_ENV'] ?? '') === 'prod')
            self::$_instance->_isCache = true;
        return;
    }

    private function _doCacheContents():void {
        self::$_instance->_contents = FileCache::getData(self::class, []);
        if(self::$_instance->_contents)
            return;

        self::$_instance->_contents = self::$_instance->_load(self::$_instance->_loadEnvFile());
        FileCache::setContents(self::class, self::$_instance->_contents);
        return;
    }

    private function _loadEnvFile():array {
        $_appEnv = $_SERVER['APP_ENV'] ?? self::DEFAULT_ENV_APP;
        $_fileData = FileManager::setFilePath(PathManager::getEnv())->read($_appEnv);
        return preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', "", explode(PHP_EOL, $_fileData));
    }

    private function _load(array $_fileData):array {
        return array_reduce($_fileData, function(object $_result, string $_data) :object {
            if(!$_data || str_starts_with($_data, SymbolicCodeEnum::SHARP->value) || str_starts_with($_data, SymbolicCodeEnum::SEMICLON->value))
                return $_result;

            list($_envKey, $_envValue) = explode(SymbolicCodeEnum::EQUAL->value, $_data, self::EXPLODE_LIMIT);
            if(!$_result->list)
                return $_result->setIniKey(self::INI_KEY)->init($_envKey, $_envValue);

            return $_result->doAddContents($_envKey, $_envValue);

        }, new class {
            use EnvTrait;

            public array $list = [];
            private string $_iniKey = '';
            private const DB_OPTION_TARGET_LIST = [
                'ATTR' => 'PDO',
                'MYSQL_ATTR' => 'MYSQL',
            ];
        })->list;
    }

}