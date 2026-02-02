<?php

namespace Nexus\Core\Libs;

use Nexus\Core\Enum\SymbolicCodeEnum;
use Nexus\Core\Libs\Traits\Property\PropertyTrait;

class FileManager
{
    use PropertyTrait;

    private string $_url = '';
    private string $_contents = '';
    private array $_deleteList = [];

    private static string $_filePath = '';
    private static ?FileManager $_fileManager = null;

    private function __construct(){}

    public function __clone():void {
        throw new \ErrorException('クローンできません。');
    }

    public static function getInstance(): self
    {
        if (is_null(self::$_fileManager))
            self::$_fileManager = new self();

        return self::$_fileManager;
    }

    public static function setFilePath(string $_filePath):self {
        self::$_filePath = $_filePath;
        self::getInstance()->_initVariable();
        return self::getInstance();
    }

    private function _initVariable(): void
    {
        foreach (get_object_vars($this) as $_key => $_val)
        {
            $_methodName = ucfirst(str_replace(SymbolicCodeEnum::UNDERBAR->value, "", $_key));
            if (gettype($_val) === 'array') {
                $this->{sprintf('set%s', $_methodName)}([]);
                continue;
            }
            $this->{sprintf('set%s', $_methodName)}("");
        }
        return;
    }

    public function read(string $_fileName): string
    {
        if (($_contents = file_get_contents($this->_doCreateFullPath($_fileName))) === false)
            throw new \ErrorException(sprintf('ファイル読み込みに失敗 : %s', $this->_doCreateFullPath($_fileName)));
        return $_contents;
    }

    public function appendWrite(string $_fileName):void {
        if (file_put_contents($this->_doCreateFullPath($_fileName), $this->getContents(), FILE_APPEND | LOCK_EX) === false)
            throw new \ErrorException(sprintf('ファイル書き込みに失敗 : %s', $this->_doCreateFullPath($_fileName)));
        return;
    }

    public function write(string $_fileName):void {
        if (file_put_contents($this->_doCreateFullPath($_fileName), $this->getContents(), LOCK_EX) === false) {
            if(is_file($this->_doCreateFullPath($_fileName)) && !unlink($this->_doCreateFullPath($_fileName)))
                throw new \ErrorException(sprintf('ファイル書き込みに失敗 : %s', $this->_doCreateFullPath($_fileName)));
        }
        return;
    }

    public function rename(string $_renameFile, string $_fileName):void {
        if (!rename($_renameFile, $this->_doCreateFullPath($_fileName))) {
            if(is_file($this->_doCreateFullPath($_fileName)) && !unlink($this->_doCreateFullPath($_fileName)))
                throw new \ErrorException(sprintf('ファイルRenameに失敗 : %s ⇨ %s', $this->_doCreateFullPath($_fileName), $_renameFile));
        }
        return;
    }

    public function delete(string $_fileName): void
    {
        if (!unlink($this->_doCreateFullPath($_fileName)))
            throw new \ErrorException(sprintf('%s ファイル削除に失敗', $this->_doCreateFullPath($_fileName)));
        return;
    }

    public function deleteAll(): void
    {
        foreach ($this->getDeleteList() as $_val) {
            if (!unlink($_val))
                throw new \ErrorException(sprintf('%s ファイル削除に失敗', $_val));
        }
        return;
    }

    private function _doCreateFullPath(string $_fileName): string
    {
        return ($this->getUrl()) ? $this->getUrl() : sprintf('%s%s', self::$_filePath, $_fileName);
    }

}