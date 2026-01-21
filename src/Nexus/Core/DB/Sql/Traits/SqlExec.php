<?php

namespace Nexus\Core\DB\Sql\Traits;

use Nexus\Core\DB\DBConnection;
use Nexus\Core\DB\Sql\Enum\PDOEnum;

trait SqlExec
{
    protected function query(string $_sql, array $_paramList, bool $_noBind = false):mixed
    {
        return $this->_doQuery($_sql, $_paramList, $_noBind);
    }

    private function _doQuery(string $_sql, array $_paramList, bool $_noBind)
    {
        $_queryMethod = PDOEnum::QUERY->query();
        $_queryForTransMethod = PDOEnum::QUERY_TRANS->query();
        if($this->isVirtual()) {
            return $this->{$_queryForTransMethod}($_sql, $_paramList);
        }
        return $this->{$_queryMethod}($_sql, $_paramList);
    }

    /**
     * SQL実行
     *
     * @param string $sql 実行するSQL
     * @param array $paramList バインドするパラメータのリスト
     * @return 実行したSQLの結果
     */
    private function _query(string $_sql, array $_paramList = []):mixed
    {
        $_result = null;
        $_dbh = DBConnection::setType()::get();
        try
        {
            $_dbh->beginTransaction();
            // SQL設定
            $_stmt = $_dbh->prepare($_sql);
            $_stmt = $this->doBind($_stmt, $_paramList);

            // SQL実行
            $_result = $_stmt->execute();

            // 解放
            $_stmt->closeCursor();
            // トランザクションコミット
            $_dbh->commit();
        }
        catch (\PDOException $_ex)
        {
            // トランザクションロールバック
            if ($_dbh->inTransaction())
                $_dbh->rollBack();

            // 例外をベースになげる
            throw $_ex;
        }
        finally
        {
            $_dbh = $_stmt = null;
        }
        return $_result;
    }

    /**
     * SQL実行：トランザクション引き渡し用
     *
     * @param connection $dbh DBコネクション
     * @param string $sql 実行するSQL
     * @param array $paramList バインドするパラメータのリスト
     * @return 実行したSQLの結果
     */
    private function _queryForTrans(string $_sql, array $_paramList = [])
    {
        $_result = null;

        // SQL設定
        $_stmt = DBConnection::get()->prepare($_sql);
        // パラメータバインド
        $_stmt = $this->doBind($_stmt, $_paramList);

        // SQL実行
        $_result = $_stmt->execute();

        // 解放
        $_stmt->closeCursor();
        $_stmt = null;
        return $_result;
    }

    private function _isInsertId(string $_sql) :bool
    {
        $_sql = mb_strtolower($_sql);
        if(str_contains($_sql, 'insert') && !str_contains($_sql, 'bulk'))
            return true;
        return false;
    }
}
