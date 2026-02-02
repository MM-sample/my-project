<?php

namespace Nexus\Core\Libs\Path;

use Nexus\Core\Enum\SymbolicCodeEnum;

class PathManager
{

    private static ?SystemPath $_systemPath = null;
    private static ?ApiSystemPath $_apiSystemPath = null;
    private static ?AppSystemPath $_appSystemPath = null;
    private static ?CronSystemPath $_cronSystemPath = null;

    public function __construct(
        SystemPath $_systemPath,
        ApiSystemPath $_apiSystemPath,
        AppSystemPath $_appSystemPath,
        CronSystemPath $_cronSystemPath,
        string $_rootPath
    ){
        self::$_systemPath = $_systemPath;
        self::$_apiSystemPath = $_apiSystemPath;
        self::$_appSystemPath = $_appSystemPath;
        self::$_cronSystemPath = $_cronSystemPath;
        $this->_init($_rootPath);
    }

    private function _init(string $_rootPath):void {
        self::$_systemPath->setSystemPath($_rootPath);
        self::$_appSystemPath->setAppPath(self::$_systemPath->getSystem());
        self::$_apiSystemPath->setApiPath(self::$_systemPath->getSystem());
        self::$_cronSystemPath->setCronPath(self::$_systemPath->getSystem());
    }

    public static function getPathTrimLast(string $_methodName) {
        return rtrim(self::{$_methodName}(), DIRECTORY_SEPARATOR);
    }

    public static function __callStatic(string $_methodName, array $_args) {
        if(!preg_match('~^(set|get)([A-Z])(.*)$~', $_methodName, $_matches))
            throw new \ErrorException(sprintf('Method Not Format : %s ', $_methodName));

        $_property = self::_creatProperty($_matches);

        switch($_matches[1]) {
            case 'set':
                return self::_setPath($_property, $_methodName, current($_args));
            case 'get':
                return self::_getPath($_property, $_methodName);
            default:
                throw new \ErrorException(sprintf('Method Not Exists : %s ', $_methodName));
        }
    }

    private static function _creatProperty(array $_matches):string {
        $_property = sprintf('%s%s%s', SymbolicCodeEnum::UNDERBAR->value, strtolower($_matches[2]), $_matches[3]);

        $_targetCollct = [
            'systemPath'     => self::$_systemPath,
            'appSystemPath'  => self::$_appSystemPath,
            'apiSystemPath'  => self::$_apiSystemPath,
            'cronSystemPath' => self::$_cronSystemPath,
        ];

        $_foundIn = [];
        foreach ($_targetCollct as $_key => $_object) {
            if (property_exists($_object, $_property)) {
                $_foundIn[] = $_key;
            }
        }

        if (!$_foundIn)
            throw new \ErrorException(sprintf('Property Not Exists : %s ', $_property));

        if (count($_foundIn) > 1 )
            throw new \ErrorException("Property Duplication in: " . implode(SymbolicCodeEnum::COMMA->value, $_foundIn));

        return $_property;

    }

    private static function _getTargetSystem():array
    {
        return [
            self::$_systemPath,
            self::$_appSystemPath,
            self::$_apiSystemPath,
            self::$_cronSystemPath,
        ];
    }

    private static function _getPath(string $_property, string $_methodName):string
    {
        foreach( self::_getTargetSystem() as $_object ) {
            if (property_exists($_object, $_property))
                return $_object->{$_methodName}();
        }
        return '';
    }

    private static function _setPath(string $_property, string $_methodName, string $_args):string
    {
        foreach(self::_getTargetSystem() as $_object) {
            if (property_exists($_object, $_property)) {
                $_object->${$_methodName}($_args);
            }
        }
        return self::class;
    }

}