<?php
namespace Nexus\Core\DB\Parts\Enum;

use Nexus\Core\Enum\Interface\EnumInterface;
use Nexus\Core\Enum\Traits\EnumTrait;

enum DnsEnum:string implements EnumInterface
{
    use EnumTrait;

    case MYSQL = 'mysql';
    case POSTGRESS = 'pgsql';

    public static function get(string $_dbTpye):string {
        return match($_dbTpye) {
            self::MYSQL->value => 'mysql:host=%s;dbname=%s;port=%s;charset=%s',
        };
    }
}
