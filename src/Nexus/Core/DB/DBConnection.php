<?php
namespace Nexus\Core\DB;

use Nexus\Core\DB\Parts\Enum\DnsEnum;
use Nexus\Core\Enum\SymbolicCodeEnum;
use Nexus\Core\Libs\Config;
use Nexus\Core\DB\Parts\PDO;

final class DBConnection
{
    /** @var int スタックが空であることを示す定数 */
    private const STACK_EMPTY = 0;

    public function __clone() {
        throw new \ErrorException('Clone is not allowed against '. get_class($this));
    }

    private static array $_instances = [];
    private static array $_sharedHandles = [];
    private static string $_dbType = '';
    private static string $_dbDriver = '';

    private function __construct()
    {
        self::$_dbDriver = self::_getConfigDB('driver');
        $this->_init();
    }

    private function _init():void {
        try {
            $_dsn = vsprintf(DnsEnum::get(self::$_dbType),
                [ self::_getConfigDB('host')
                 ,self::_getConfigDB('database')
                 ,self::_getConfigDB('port')
                 ,self::_getConfigDB('charset')]);
            $_options = self::_setOption();

            self::$_instances[self::$_dbType] = new PDO(
                 $_dsn
                ,self::_getConfigDB('username')
                ,self::_getConfigDB('password')
                ,$_options
            );

        }catch(\PDOException $_ex) {
            self::$_instances[self::$_dbType] = null;
            throw $_ex;
        }
    }

    private static function _getConfigDB(string $_target):string|array {
        return Config::get(sprintf('db.connections.%s.%s', self::$_dbType, $_target));
    }

    private static function _setOption() :array
    {
        $_replaceTarget = sprintf('%s%s', self::$_dbDriver, SymbolicCodeEnum::UNDERBAR->value);
        $_ret = [];
        foreach(self::_getConfigDB('options') as $_attrKey => $_attrValue) {
            if(str_starts_with($_attrValue, self::$_dbDriver))
                $_attrValue = constant(sprintf("\%s::%s", self::$_dbDriver, str_replace($_replaceTarget, '', $_attrValue)));

            $_attrKey   = constant(sprintf("\%s::%s", self::$_dbDriver, $_attrKey));
            $_ret[$_attrKey] = $_attrValue;
        }
        return $_ret;
    }

    /**
     * PDOインスタンスの取得
     */
    public static function get() :PDO {
        // 共有ハンドルの有無を確認 (self::$_dbType を使用)
        if (self::_hasSharedHandle()) {
            return end(self::$_sharedHandles[self::$_dbType]);
        }

        if(!isset(self::$_instances[self::$_dbType]))
            new self();

        return self::$_instances[self::$_dbType];
    }

    /**
     * 現在のDB種別に共有ハンドルが積まれているか判定
     */
    private static function _hasSharedHandle(): bool {
        return isset(self::$_sharedHandles[self::$_dbType]) && count(self::$_sharedHandles[self::$_dbType]) > self::STACK_EMPTY;
    }

    public static function setType(?string $_dbType = null) :string {
        self::$_dbType = (is_null($_dbType))? Config::get('db.default') : $_dbType;
        return self::class;
    }

    /**
     * 共有ハンドルの登録（現在の型に対してスタックへ積む）
     */
    public static function setSharedHandle(\PDO $_dbh): void {
        if (!isset(self::$_sharedHandles[self::$_dbType])) {
            self::$_sharedHandles[self::$_dbType] = [];
        }
        self::$_sharedHandles[self::$_dbType][] = $_dbh;
    }

    /**
     * 共有ハンドルの解除（現在の型からスタックを降ろす）
     */
    public static function releaseSharedHandle(): void {
        if (self::_hasSharedHandle()) {
            array_pop(self::$_sharedHandles[self::$_dbType]);
        }
    }
}