<?php
namespace codeview\VerCtrl;

class CommitLogInfo
{
    private $commitId;
    private $parentId;
    private $parentId2;
    private $subject;
    private $date;
    private $author;
    private $opePaths = array();

    /**
     * コンストラクタ
     * @param  string $commit_id  コミットログの一意のID
     * @param  string $commitShortId コミットログの一意のショートID
     * @param  string $parentId   親のID
     * @param  string $date       日付
     * @param  string $author     作者
     * @param  string $subject    コミットメッセージのタイトル
     * @param  string $parentId2  親のID2(マージが発生した場合に設定)
     */
    public function __construct(
        string $commitId,
        string $commitShortId,
        string $parentId,
        string $date,
        string $author,
        string $subject,
        string $parentId2
    ) {
        $this->commitId = $commitId;
        $this->commitShortId = $commitShortId;
        $this->subject = $subject;
        $this->parentId = $parentId;
        $this->parentId2 = $parentId2;
        $this->date = $date;
        $this->author = $author;
    }

    /**
     * コミット情報のリストの比較
     * @param array $leftList
     * @param array $rightList
     * @return 比較結果[appended:追加された一覧, deleted:削除された一覧]
     */
    public static function compair(array $leftList, array $rightList): array
    {
        $result = [];
        $appended = [];
        $deleted = [];

        foreach ($leftList as $left) {
            $isExist = false;
            foreach ($rightList as $right) {
                if ($left->getCommitId() === $right->getCommitId()) {
                    $isExist = true;
                }
            }
            if (!$isExist) {
                $deleted[] = $left;
            }
        }
        foreach ($rightList as $right) {
            $isExist = false;
            foreach ($leftList as $left) {
                if ($left->getCommitId() === $right->getCommitId()) {
                    $isExist = true;
                }
            }
            if (!$isExist) {
                $appended[] = $right;
            }
        }
        $result['appended'] = $appended;
        $result['deleted'] = $deleted;
        return $result;
    }

    /**
     * @return コミットログのID
     */
    public function getCommitId() : string
    {
        return $this->commitId;
    }
    /**
     * @return コミットログのショートID
     */
    public function getCommitShortId() : string
    {
        return $this->commitShortId;
    }
    /**
     * @return コミットログのタイトル
     */
    public function getSubject() : string
    {
        return $this->subject;
    }

    /**
     * @return 親のID
     */
    public function getParentId() : string
    {
        return $this->parentId;
    }

    /**
     * @return 親のID2(マージがある時のみ)
     */
    public function getParentId2() : string
    {
        return $this->parentId2;
    }

    /**
     * @return ファイルパス一覧
     */
    public function getOpePaths() : array
    {
        return $this->opePaths;
    }

    /**
     * @param object $opePathInfo パス情報
     */
    public function pushOpePaths(object $opePathInfo) : void
    {
        $this->opePaths[] = $opePathInfo;
    }
    public function setOpePaths(array $opePathInfoList)
    {
        $this->opePaths = $opePathInfoList;
    }
    public function getDate() : string
    {
        return $this->date;
    }
    public function getAuthor() : string
    {
        return $this->author;
    }
}
