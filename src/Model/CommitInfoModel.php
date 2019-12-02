<?php
namespace codeview\Model;

/**
 * コミット情報の記録
 */
class CommitInfoModel extends ModelBase
{
    protected $opePathModel;

    public function __construct($app, $database)
    {
        parent::__construct($app, $database);
        $this->opePathModel = new OpePathInfoModel($app, $database);
    }

    /**
     * データベースの設定を行う.
     */
    public function setup() : void
    {
        $this->database->exec(
            "CREATE TABLE IF NOT EXISTS commit_info (
                commit_id TEXT,
                commit_short_id TEXT,
                repository_id NUMBER,
                seq NUMBER,
                subject TEXT,
                parent_id TEXT,
                parent_id2 TEXT,
                date TEXT,
                author TEXT,
                PRIMARY KEY(commit_id, repository_id),
                FOREIGN KEY (repository_id) REFERENCES repository(id)
            );"
        );
        $this->opePathModel->setup();
    }

    /**
     * コミットの情報
     * @param  int $repositoryId  リポジトリID
     * @return コミットの情報の一覧
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function get(int $repositoryId, bool $useOpePath = true) : array
    {
        $records = \ORM::for_table('commit_info')
            ->where_equal('repository_id', $repositoryId)
            ->order_by_desc('seq')
            ->find_many();
        return $this->convertRecordToCommitLogInfo($repositoryId, $records, $useOpePath);
    }
    /**
     * コミットの情報
     * @param  int $repositoryId  リポジトリID
     * @param  string $repositoryId  リポジトリID
     * @return コミットの情報の一覧
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getByCommitId(int $repositoryId, string $commitId, bool $useOpePath = true) : array
    {
        $records = \ORM::for_table('commit_info')
            ->where_equal('repository_id', $repositoryId)
            ->where_equal('commit_id', $commitId)
            ->order_by_desc('seq')
            ->find_many();
        return $this->convertRecordToCommitLogInfo($repositoryId, $records, $useOpePath);
    }
    public function count(int $repositoryId) : int
    {
        return \ORM::for_table('commit_info')
            ->where_equal('repository_id', $repositoryId)
            ->count();
    }
    /**
     * コミットの情報をページで取得する
     * @param  string $repositoryId  リポジトリID
     * @return コミットの情報の一覧
     */
    public function getPage(int $repositoryId, int $offset, int $limit, bool $useOpePath) : array
    {
        $records = \ORM::for_table('commit_info')
            ->where('repository_id', $repositoryId)
            ->order_by_desc('seq')
            ->limit($limit)
            ->offset($offset)
            ->find_many();
        return $this->convertRecordToCommitLogInfo($repositoryId, $records, $useOpePath);
    }
    /**
     * commit_infoテーブルのレコードセットをCommitLogInfoの配列に変換する
     * @param int $repositoryId 登録するレポジトリID
     * @param array $records commit_infoテーブルのレコード
     * @return CommitLogInfoの配列
     */
    protected function convertRecordToCommitLogInfo(int $repositoryId, array $records, bool $useOpePath) : array
    {
        $result = [];
        foreach ($records as $rec) {
            $obj = new \codeview\VerCtrl\CommitLogInfo(
                $rec->commit_id,
                $rec->commit_short_id,
                $rec->parent_id,
                $rec->date,
                $rec->author,
                $rec->subject,
                $rec->parent_id2,
            );
            if ($useOpePath) {
                $obj->setOpePaths(
                    $this->opePathModel->getByCommitId($repositoryId, $rec->commit_id)
                );
            }
            $result[] = $obj;
        }
        return $result;
    }

    /**
     * コミットの情報削除
     * @param  int $repositoryId  リポジトリID
     * @return コミットの情報の一覧
     */
    public function deleteByRepositoryId(int $repositoryId) : void
    {
        //$this->database->beginTransaction();

        $this->opePathModel->deleteByRepositoryId($repositoryId);

        \ORM::for_table('commit_info')
            ->where_equal('repository_id', $repositoryId)
            ->delete_many();
        //$this->database->commit();
    }

    /**
     * コミットの情報削除
     * @param  int $repositoryId  リポジトリID
     * @param  string $repositoryId  コミットID
     * @return コミットの情報の一覧
     */
    public function deleteByCommitId(int $repositoryId, string $commitId) : void
    {
        //$this->database->beginTransaction();

        $this->opePathModel->deleteByCommitId($repositoryId, $commitId);

        \ORM::for_table('commit_info')
            ->where(
                array(
                    'repository_id' => (int) $repositoryId,
                    'commit_id' => $commitId
                )
            )
            ->delete_many();
        //$this->database->commit();
    }

    /**
     * リポジトリ情報を登録
     * @param int $repositoryId レポジトリのID
     * @param array $commitList CommitLogInfoの配列
     * @data 登録情報
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function append(int $repositoryId, array $commitList) : void
    {
        //$this->database->beginTransaction();
        $maxSeq = \ORM::for_table('commit_info')
            ->where_equal('repository_id', $repositoryId)
            ->max('seq');
        if (is_null($maxSeq)) {
            $maxSeq = 1;
        } else {
            ++$maxSeq;
        }
        foreach (array_reverse($commitList) as $info) {
            $row = \ORM::for_table('commit_info')->create();
            $row->commit_id = $info->getCommitId();
            $row->commit_short_id = $info->getCommitShortId();
            $row->repository_id = $repositoryId;
            $row->subject = $info->getSubject();
            $row->parent_id = $info->getParentId();
            $row->parent_id2 = $info->getParentId2();
            $row->date = $info->getDate();
            $row->author = $info->getAuthor();
            $row->seq = $maxSeq;
            $row->save();
            $this->opePathModel->append($repositoryId, $row->commit_id, $info->getOpePaths());
            $maxSeq = $maxSeq + 1;
        }
        //$this->database->commit();
    }
}
