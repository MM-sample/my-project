<?php
namespace Nexus\Core\DB\Sql\Enum;

enum SqlEnum :string
{
    case SELECT = 'select';
    case SELECT_CNT = 'select_cnt';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case INSERT = 'insert';
    case BULK_UPDATE = 'bulk_update';
    case BULK_UPDATE_PARTS = 'bulk_update_parts';
    case BASE_SELECT = 'base_select';
    case BASE_INSERT = 'base_insert';
    case UPSERT = 'upsert';

    public function query():string {
        return match($this) {
            self::SELECT => 'SELECT * FROM %s',
            self::SELECT_CNT => 'SELECT count( %s ) %s FROM %s',
            self::UPDATE => 'UPDATE %s SET update_timestamp = NOW(), %s',
            self::DELETE => 'DELETE FROM %s',
            self::INSERT => 'INSERT INTO %s ( create_timestamp, %s ) VALUES  %s ',
            self::BULK_UPDATE => 'UPDATE %s SET %s WHERE %s IN (%s)',
            self::BASE_SELECT => 'SELECT %s FROM %s',
            self::BASE_INSERT => 'INSERT INTO %s ( %s ) ( %s ) ',
            self::UPSERT => 'INSERT INTO %s ( create_timestamp, %s ) VALUES  %s ON DUPLICATE KEY UPDATE update_timestamp = NOW(), %s ',
        };
    }

    public function getParts():string {
        return match($this) {
            self::BULK_UPDATE_PARTS => '%s = ELT(FIELD(%s,%s), %s )',
        };
    }
}