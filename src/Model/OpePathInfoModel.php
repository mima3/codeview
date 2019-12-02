<?php
namespace codeview\Model;

/**
 * 操作パス情報の記録
 */
class OpePathInfoModel extends ModelBase
{
    /**
     * データベースの設定を行う.
     */
    public function setup() : void
    {
        $this->database->exec(
            "CREATE TABLE IF NOT EXISTS opepath_info (
                repository_id NUMBER,
                commit_id TEXT,
                path TEXT,
                path2 TEXT,
                ope TEXT,
                PRIMARY KEY(commit_id, repository_id, path),
                FOREIGN KEY (repository_id) REFERENCES repository(id),
                FOREIGN KEY (commit_id) REFERENCES commit_info(commit_id)
            );"
        );
    }

    public function getByCommitId(int $repositoryId, string $commitId) : array
    {
        $records = \ORM::for_table('opepath_info')
            ->where(
                array(
                    'repository_id' => (int) $repositoryId,
                    'commit_id' => $commitId
                )
            )
            ->order_by_asc('path')
            ->find_many();
        $result = [];
        foreach ($records as $rec) {
            $obj = new \codeview\VerCtrl\OpePathInfo(
                $rec->ope,
                $rec->path,
                $rec->path2
            );
            $result[] = $obj;
        }
        return $result;
    }
    public function append(int $repositoryId, string $commitId, array $opePathList) : void
    {
        //$this->database->beginTransaction();
        foreach ($opePathList as $info) {
            $row = \ORM::for_table('opepath_info')->create();
            $row->repository_id = $repositoryId;
            $row->commit_id = $commitId;
            $row->path = $info->getPath();
            $row->path2 = $info->getPath2();
            $row->ope = $info->getOpe();
            $row->save();
        }
        //$this->database->commit();
    }

    public function deleteByRepositoryId(int $repositoryId) : void
    {
        \ORM::for_table('opepath_info')
            ->where_equal('repository_id', $repositoryId)
            ->delete_many();
    }

    public function deleteByCommitId(int $repositoryId, string $commitId) : void
    {
        \ORM::for_table('opepath_info')
        ->where(
            array(
                'repository_id' => $repositoryId,
                'commit_id' => $commitId
            )
        )
        ->delete_many();
    }
}
