<?php

namespace Nexus\Core\Libs;

use Nexus\Core\Enum\PermissionEnum;

class DirManager
{
    private static string $_path = '';
    private static ?DirManager $_instance = null;

    private const DIR_TYPE_METHOD = [
        'FILE' => 'isFile',
        'DIR'  => 'isDir'
    ];

    private function __construct(){}

    public function __clone():void {
        throw new \ErrorException('クローンできません。');
    }

    /**
     * set Instance
     *
     * @return self
     */
    public static function getInstance():self {
        if(is_null(self::$_instance))
            self::$_instance = new self();

        return self::$_instance;
    }

    /**
     * set Path
     *
     * @param string $_path
     * @return self
     */
    public static function setPath(string $_path):self {
        self::$_path = $_path;
        return self::getInstance();
    }

    /**
     * make Directory
     *
     * @param integer $_mode
     * @return void
     */
    public function make(?int $_mode = null):void {
        if(is_dir(self::$_path) || !self::$_path)
            return;

        if(is_null($_mode))
            $_mode = PermissionEnum::STANDARD->value;

        if(!mkdir(self::$_path, $_mode, true))
            throw new \ErrorException(sprintf('%s : ディレクトリ作成に失敗', $this->_path));

        if (!chmod(self::$_path, $_mode))
            throw new \ErrorException(sprintf('%s : modeの設定に失敗', $this->_path));
        return;
    }

    /**
     * get Dirctory File List
     *
     * @return array
     */
    public function getListAll():array {
        return $this->_doFindListAll();
    }

    /**
     * get File List
     *
     * @return array
     */
    public function getFileList():array {
        return $this->_doFindListAll(self::DIR_TYPE_METHOD['FILE']);
    }

    /**
     * get Directory List
     *
     * @return array
     */
    public function getDirList():array {
        return $this->_doFindListAll(self::DIR_TYPE_METHOD['DIR']);
    }

    private function _doFindListAll(?string $_methodName = null):array  {
        $_iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                self::$_path,
                \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::KEY_AS_PATHNAME |
                \FilesystemIterator::CURRENT_AS_FILEINFO
            ), \RecursiveIteratorIterator::SELF_FIRST
        );

        $_ret = [];
        foreach($_iterator as $_key => $_splFileInfo) {
            if (is_null($_methodName)) {
                $_ret[$_key] = $_splFileInfo;
                continue;
            }

            if ($_splFileInfo->{$_methodName}())
                $_ret[$_key] = $_splFileInfo;
        }
        return $_ret;
    }

}