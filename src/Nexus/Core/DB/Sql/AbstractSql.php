<?php

namespace Nexus\Core\DB\Sql;

use Nexus\Core\DB\DBConnection;
use Nexus\Core\DB\Sql\Traits\SqlExec;
use Nexus\Core\DB\Sql\Traits\SqlHelper;

abstract class AbstractSql
{
    use SqlExec, SqlHelper;

    private string $_query     = "";
    private ?string $_dbType   = null;
    private bool $_whereClause = false;
    private bool $_isUpsert    = false;

    protected array  $columns   = [];
    protected string $tableName = '';
    protected const UPSERT_INS_PREFIX = '_ins';
    protected const UPSERT_UP_PREFIX = '_up';

    /**
     * 利用するDB種別をセット
     */
    public function setTypeDB(?string $_dbType = null): self
    {
        $this->_dbType = $_dbType;
        return $this;
    }

    /**
     * DBコネクションの取得
     */
    final protected function getDB(): \PDO
    {
        return DBConnection::setType($this->_dbType)::get();
    }

    /**
     * トランザクション中（仮想実行中）か判定
     */
    final protected function isVirtual(): bool
    {
        return $this->getDB()->inTransaction();
    }

    /**
     * オーバーライドしてトランザクション内のクエリ処理を記述
     */
    protected function virtualQuery(\PDO $_dbh): bool
    {
        return true;
    }

    /**
     * SQL実行：トランザクション境界の制御
     */
    public function virtualMethod(?string $_method = null, array $_data = []): mixed
    {
        $_result = null;

        // 1. 型をセットしてコネクションを取得
        $_dbh = $this->getDB();
        try
        {
            // 2. 共有スタックに積む（内部で現在のdbTypeが参照されるため引数不要）
            DBConnection::setSharedHandle($_dbh);

            // 3. トランザクション制御（ネスト考慮）
            $_isFirstLayer = !$_dbh->inTransaction();
            if ($_isFirstLayer) {
                $_dbh->beginTransaction();
            }

            // 4. メソッドの実行
            if (is_null($_method)) {
                $_result = $this->virtualQuery($_dbh);
            } else {
                if (!method_exists($this, $_method)) {
                    throw new \PDOException(sprintf('実行するメソッド [%s] が見つかりません。', $_method));
                }
                $_result = call_user_func_array([$this, $_method], [$_data]);
            }

            // 5. 自分が開始したレイヤーならコミット
            if ($_isFirstLayer) {
                $_dbh->commit();
            }
        }
        catch (\PDOException $_ex)
        {
            // 自分が開始したレイヤーならロールバック
            if (isset($_dbh) && $_dbh->inTransaction()) {
                $_dbh->rollBack();
            }
            throw $_ex;
        }
        finally
        {
            // 6. 共有スタックから解除（引数不要）
            DBConnection::releaseSharedHandle();
        }
        return $_result;
    }
}