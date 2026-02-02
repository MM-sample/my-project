<?php

namespace Nexus\Core\Libs\Cache;

use Nexus\Core\Libs\FileManager;
use Nexus\Core\Libs\Path\PathManager;
use Nexus\Core\Libs\Traits\Property\PropertyStaticTrait;

class FileCache
{
    use PropertyStaticTrait;

    private static mixed $_contents = "";
    private static array $_cacheList = [];

    public static function setContents(string $_fileName, mixed $_contents):string {
        self::$_contents = $_contents;
        self::_store(md5($_fileName));
        self::$_cacheList[$_fileName] = $_contents;
        return self::class;
    }

    public static function getData(string $_fileName, mixed $_default=''):mixed {
        if(!self::_isLoad($_fileName))
            return $_default;

        return self::getCacheList()[$_fileName];
    }

    private static function _isLoad(string $_fileName):bool {

        if (array_key_exists($_fileName, self::getCacheList()))
            return true;

        $_cacheFileName = md5($_fileName);
        if(self::_isCached($_cacheFileName)) {
            self::$_cacheList[$_fileName] = self::_doFileLoad($_cacheFileName);
            return true;
        }
        return false;
    }

    private static function _isCached(string $_cacheFileName):bool {
        $_cacheFilePath = sprintf('%s%s', PathManager::getCache(), $_cacheFileName);
        clearstatcache();
        return is_readable($_cacheFilePath);
    }

    private static function _doFileLoad(string $_cacheFileName):mixed {
        try {
            return unserialize(FileManager::setFilePath(PathManager::getCache())->read($_cacheFileName));
        } catch (\Throwable $_ex) {
            return false;
        }
    }

    private static function _store(string $_cacheFileName):void {
        FileManager::setFilePath(PathManager::getCache())
                   ->setContents(serialize(self::getContents()))->write($_cacheFileName);
        return;
    }

}