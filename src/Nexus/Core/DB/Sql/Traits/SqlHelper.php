<?php

namespace Nexus\Core\DB\Sql\Traits;

use Nexus\Core\DB\Sql\AbstractSql;
use Nexus\Core\DB\Sql\Enum\SqlEnum;
use Nexus\Core\Enum\SymbolicCodeEnum;

trait SqlHelper
{
    final protected function getQuery(): string { return $this->_query; }

    final protected function setQuery(string $_query): self {
        $this->_query = $_query;
        return $this;
    }

    final protected function setWhereClause(bool $_args = false): self {
        $this->_whereClause = $_args;
        return $this;
    }

    private function _getTableName(): string {
        return str_replace('Model', "", $this->tableName);
    }

    /**
     * 基本的なInsert文の作成
     */
    final protected function createInsertSql(array $_data): self {
        $this->setQuery("")->setWhereClause();
        $_column = $_bindColumn = [];
        foreach (array_keys($_data) as $_hashKey) {
            if (!in_array($_hashKey, $this->columns)) continue;
            $_column[] = $_hashKey;
            $_bindColumn[] = sprintf(":%s", $_hashKey);
        }
        $this->setQuery(sprintf(SqlEnum::INSERT->query(), $this->_getTableName(), implode(', ', $_column), sprintf('( NOW(), %s )', implode(", ", $_bindColumn))));
        return $this;
    }

    /**
     * 高速なBulkInsert文の作成
     */
    final protected function createBulkInsertSql(array $_data): self {
        $this->setQuery("")->setWhereClause();
        $_column = $_tmpColumn = $_bindColumn = [];
        foreach ($_data as $_key => $_val) {
            foreach (array_keys($_val) as $_hashKey) {
                if (!in_array($_hashKey, $this->columns)) continue;
                $_column[] = $_hashKey;
                $_tmpColumn[] = sprintf(":%s%s", $_hashKey, $_key);
            }
            $_bindColumn[] = sprintf('( NOW(), %s )', implode(', ', $_tmpColumn));
            $_tmpColumn = [];
        }
        $this->setQuery(sprintf(SqlEnum::INSERT->query(), $this->_getTableName(), implode(', ', array_unique($_column)), implode(", ", $_bindColumn)));
        return $this;
    }

    /**
     * ON DUPLICATE KEY UPDATE (Upsert) 文の作成
     */
    final protected function createUpsertSql(array $_insertData, array $_updateData): self {
        $this->setQuery("")->setWhereClause();
        $this->setIsUpsert(true);

        $_insertColumn = $_insertBind = [];
        foreach (array_keys($_insertData) as $_hashKey) {
            if (!in_array($_hashKey, $this->columns)) continue;
            $_insertColumn[] = $_hashKey;
            $_insertBind[] = sprintf(":%s%s", $_hashKey, AbstractSql::UPSERT_INS_PREFIX);
        }

        $_updateColumn = [];
        foreach (array_keys($_updateData) as $_hashKey) {
            if (!in_array($_hashKey, $this->columns)) continue;
            // インクリメント制御 (SqlHelper内蔵ロジック)
            if ($_strQuery = $this->_createIncDecrement($_hashKey, 'increment')) {
                $_updateColumn[] = $_strQuery;
                continue;
            }
            $_updateColumn[] = sprintf('%s = :%s%s', $_hashKey, $_hashKey, AbstractSql::UPSERT_UP_PREFIX);
        }

        $this->setIsUpsert(false)->setQuery(sprintf(SqlEnum::UPSERT->query(), $this->_getTableName(), implode(', ', $_insertColumn), sprintf('( NOW(), %s )', implode(", ", $_insertBind)), implode(", ", $_updateColumn)));
        return $this;
    }

    private function _createIncDecrement(string $_hashKey, string $_target): string {
        $_prefix = "%s" . AbstractSql::UPSERT_UP_PREFIX;
        $_addSql = [
            'increment' => "%s = %s + :{$_prefix}",
            'decrement' => "%s = GREATEST( %s - :{$_prefix}, 0 )"
        ];
        if (str_contains($_hashKey, $_target)) {
            $_column = implode('_', array_filter(explode('_', $_hashKey), fn($_v) => $_v !== $_target));
            return sprintf($_addSql[$_target], $_column, $_column, $_hashKey);
        }
        return "";
    }

    /**
     * 汎用Where句の作成
     */
    final protected function where(string $_column, string $_operator = '=', bool $_noBind = false): self {
        $_prefix = $this->_whereClause ? ' AND ' : ' WHERE ';
        $this->setWhereClause(true);
        $_sql = sprintf('%s%s %s ' . ($_noBind ? '?' : ':%s'), $_prefix, $_column, $_operator, $this->_checkAliasColumn($_column));
        $this->setQuery($this->getQuery() . $_sql);
        return $this;
    }

    private function _checkAliasColumn(string $_column): string {
        if (!str_contains($_column, SymbolicCodeEnum::DOT->value)) return $_column;
        $_ret = explode(SymbolicCodeEnum::DOT->value, $_column);
        return $_ret[array_key_last($_ret)];
    }

    /**
     * PDOバインド用データの生成
     */
    final protected function createBindData(array $_data = [], array $_bindList = [], string $_addPrefix = ''): array {
        $_ret = [];
        $_targetList = $_bindList ?: $this->columns;
        foreach ($_data as $_hash => $_val) {
            if (!in_array($_hash, $_targetList)) continue;
            $_ret[SymbolicCodeEnum::COLON->value . $_hash . $_addPrefix] = $_val;
        }
        return $_ret;
    }

    /**
     * PDOStatementへの一括バインド
     */
    final protected function doBind(\PDOStatement $_stmt, array $_paramList): \PDOStatement {
        foreach ($_paramList as $_hash => $_val) {
            $_type = is_int($_val) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
            $_stmt->bindValue($_hash, $_val, $_type);
        }
        return $_stmt;
    }

    /**
     *  PDO BulkInsert バインド用に配列を生成
     * @param array $_data
     * @param array $_ignoreList
     * @return array
     */
    final protected function createBulkBindData(array $_data =[], array $_bindList = []) :array
    {
        $_ret =[];
        $_targetList = $this->columns;
        if ($_bindList)
            $_targetList = $_bindList;
        foreach ($_data as $_key => $_val) {
            foreach(array_keys($_val) as $_hash) {
                if (!in_array($_hash, $_targetList))
                    continue;
                $_ret[$_hash.$_key] = $_val[$_hash];
            }
        }
        return $_ret;
    }
}