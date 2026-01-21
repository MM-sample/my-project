<?php

namespace Nexus\App\Sql;
use Nexus\Core\DB\Sql\AbstractSql;

class TestSql extends AbstractSql
{
    protected string $tableName = 'test';
    protected array $columns = ['name', 'kana'];

    public function register(array $data):bool
    {
        // 1. バルクインサート用のSQLを生成
        // (SqlHelperが内部で :name0, :name1... とバインド変数を自動構成する)
        $this->createBulkInsertSql($data);

        // 2. バルク用のバインドデータを生成
        // (これもSqlHelperが多次元配列をフラットなバインド用配列に変換する)
        $bindData = $this->createBulkBindData($data);
        return $this->query($this->getQuery(), $bindData);
    }
}
