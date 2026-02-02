<?php
namespace Nexus\Core\Libs;

use Nexus\Core\Enum\SymbolicCodeEnum;
use Nexus\Core\Environment\Env;
use Nexus\Core\Libs\Cache\FileCache;
use Nexus\Core\Libs\Path\PathManager;

class Config
{
    private static array $_contents = [];

    /**
     * Undocumented function
     *
     * @param string $_key
     * @return array|integer|string
     */
    public static function get(string $_key):array|int|string|bool {
        self::_init();
        return array_reduce(explode(SymbolicCodeEnum::DOT->value, $_key), function(object $_result, string $_item) use($_key) :object  {

            if(!$_result->contents)
                return $_result->addContents($_item, $_key, self::$_contents);

            return $_result->addContents($_item, $_key);
        }, new class {
            public string|array|int|bool $contents = '';

            public function addContents(string $_item, string $_key, ?array $_list = null):self {
                $_list = (is_null($_list))? $this->contents : $_list;
                $this->_doCheckKeyExists($_item, $_key, $_list);
                $this->contents = $_list[$_item];
                return $this;
            }

            private function _doCheckKeyExists(string $_item, string $_key, ?array $_list = null):void {
                $_list = (is_null($_list))? $this->contents : $_list;
                if(!isset($_list[$_item]))
                    throw new \ErrorException(sprintf('指定した %s はconfigに定義されていません。', $_key));
                return;
            }
        })->contents;
    }

    private static function _init():void {
        (Env::get('APP_CONFIG_CACHE', false))?  self::_doFileCache() : self::_defaultCache();
    }

    private static function _defaultCache():void {
        if(!self::$_contents)
            self::_setContents();
        return;
    }

    private static function _doFileCache() :void {
        self::$_contents = FileCache::getData(self::class, []);
        if(self::$_contents)
            return;

        self::_setContents();
        FileCache::setContents(self::class, self::$_contents);
        return;
    }

    private static function _setContents():void {
        $_targetPath = PathManager::getConfig();
        foreach(DirManager::setPath($_targetPath)->getFileList() as $_fileInfo) {
            self::_doSetConfigContents($_targetPath, $_fileInfo);
        }
    }

    private static function _doSetConfigContents(string $_path, \SplFileInfo $_fileInfo):void {
        $_tmpPath = str_replace($_path, '', $_fileInfo->getRealPath());

        if(!str_contains($_tmpPath, DIRECTORY_SEPARATOR)) {
            self::$_contents[current(explode(SymbolicCodeEnum::DOT->value, $_tmpPath))] = require $_fileInfo->getRealPath();
            return;
        }

        $_pathList = str_replace(sprintf('.%s', pathinfo($_tmpPath)['extension']), '', explode(DIRECTORY_SEPARATOR, $_tmpPath));
        self::$_contents = array_merge_recursive(self::$_contents, self::_doCreateTreeList($_pathList, $_fileInfo->getRealPath()));
        return;
    }

    private static function _doCreateTreeList(array $_list, string $_filePath):array {
        $_ret = [];
        $_tmpList= &$_ret;
        $_listCnt = count($_list);
        for($i = 0; $i < $_listCnt; $i++) {
            $_tmpList = [$_list[$i] => ((int)$i === $_listCnt - 1) ? require $_filePath : ''];
            $_tmpList = &$_tmpList[$_list[$i]];
        }
        return $_ret;
     }
}