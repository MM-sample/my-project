<?php
namespace Nexus\Core\DB\Sql\Enum;

enum PDOEnum :string
{

    case MASTER = 'master';
    case SALVE = 'slave';

    case QUERY = 'query';
    case QUERY_TRANS = 'query_trans';

    case FETCH = 'fetch';
    case FETCH_TRANS = 'fetch_trans';

    case FETCH_ALL = 'fetch_all';
    case FETCH_TRANS_ALL = 'fetch_trans_all';


    public function query() :string {
        return match($this) {
            self::QUERY => '_query',
            self::QUERY_TRANS => '_queryForTrans',
        };
    }

    public function fetch() :string {
        return match($this) {
            self::FETCH => '_fetch',
            self::FETCH_TRANS => '_fetchForTrans',
        };
    }

    public function fetchAll() :string {
        return match($this) {
            self::FETCH_ALL => '_fetchAll',
            self::FETCH_TRANS_ALL => '_fetchAllForTrans',
        };
    }

}