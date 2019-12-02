<?php
namespace codeview\VerCtrl;

class OpePathInfo
{
    private $ope;
    private $path;
    private $path2;
    public const OPE_ADD = 1;
    public const OPE_DELTE = 2;
    public const OPE_MODIFY = 3;
    public const OPE_MOVE= 4;

    /**
     * GitのログよりOpePathInfoを構築する
     * @param  string $line 行情報
     * @return 作成されたOpePathInfo
     * @throws 例外についての記述
     */
    public static function build(string $line)
    {
        $ope = '';

        // 制御コード+タブ+パスの最低3バイトなければ例外とする
        // https://git-scm.com/docs/git-status
        // https://stackoverflow.com/questions/53056942/git-diff-name-status-what-does-r100-mean
        if (strlen($line) < 3) {
            throw new OpePathInfoException($line);
        }
        if (mb_strpos($line, "A\t") === 0) {
            // 追加
            return new OpePathInfo(OpePathInfo::OPE_ADD, mb_substr($line, 2));
        } elseif (mb_strpos($line, "M\t") === 0) {
            // 更新
            return new OpePathInfo(OpePathInfo::OPE_MODIFY, mb_substr($line, 2));
        } elseif (mb_strpos($line, "D\t") === 0) {
            // 削除
            return new OpePathInfo(OpePathInfo::OPE_DELTE, mb_substr($line, 2));
        } elseif (mb_strpos($line, "R") === 0 && mb_strpos($line, "\t") === 4) {
            // R000\tFromファイルパス TOファイルパス
            // 移動を表す。数値は類似度。100は移動。ファイルを書き換えると減っていく。
            $paths = explode("\t", mb_substr($line, 5));
            if (count($paths) !== 2) {
                throw new OpePathInfoException($line);
            }
            return new OpePathInfo(OpePathInfo::OPE_MOVE, $paths[0], $paths[1]);
        } else {
            throw new OpePathInfoException($line);
        }
    }

    /**
     * コンストラクタ
     * @param  string $ope  操作コード
     * @param  string $path パス(移動の時は移動元)
     * @param  string $path2 パス2(移動の時は移動先)
     */
    public function __construct(string $ope, string $path, string $path2 = '')
    {
        $this->ope = $ope;
        $this->path = $path;
        $this->path2 = $path2;
    }

    /**
     * 操作コード取得
     * @return string 操作コード
     */
    public function getOpe() : string
    {
        return $this->ope;
    }

    /**
     * 操作パス取得
     * @return string 操作パス
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * 操作パス2取得
     * @return string 操作パス
     */
    public function getPath2() : string
    {
        return $this->path2;
    }
}
