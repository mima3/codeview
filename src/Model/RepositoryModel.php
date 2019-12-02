<?php
namespace codeview\Model;

/**
 * リポジトリ情報の記録
 */
class RepositoryModel extends ModelBase
{
    /**
     * データベースの設定を行う.
     */
    public function setup() : void
    {
        $this->database->exec(
            "CREATE TABLE IF NOT EXISTS repository (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                remort TEXT,
                branch TEXT,
                local TEXT,
                name TEXT,
                type TEXT,
                head_date TEXT,
                head_id TEXT
            );"
        );
    }
    /**
     * レポジトリの情報
     * @return レポジトリの情報の一覧
     */
    public function getById($repositoryId) : object
    {
        $result = \ORM::for_table('repository')
            ->where_equal('id', $repositoryId)
            ->find_one();
        return $result;
    }
    /**
     * レポジトリの情報
     * @return レポジトリの情報の一覧
     */
    public function get() : array
    {
        $result = \ORM::for_table('repository')
            ->order_by_asc('id')
            ->find_many();
        return $result;
    }

    /**
     * リポジトリ情報を登録
     * @param int $month 月
     * @data 登録情報
     */
    public function append($remort, $local, $branch, $name, $type, $headDate, $headId) : object
    {
        // コミットは外で行うこと
        //$this->database->beginTransaction();
        $row = \ORM::for_table('repository')->create();
        $row->remort = $remort;
        $row->local = $local;
        $row->branch = $branch;
        $row->name = $name;
        $row->type = $type;
        $row->head_date = $headDate;
        $row->head_id = $headId;
        $row->save();
        //$this->database->commit();
        return $row;
    }

    public function delete(int $repositoryId) : void
    {
        //$this->database->beginTransaction();

        \ORM::for_table('repository')
            ->where_equal('id', $repositoryId)
            ->delete_many();
        //$this->database->commit();
    }
}
