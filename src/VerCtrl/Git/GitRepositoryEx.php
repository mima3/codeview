<?php
namespace codeview\VerCtrl\Git;

use \codeview\VerCtrl as VerCtrl;

class GitRepositoryEx extends \Cz\Git\GitRepository
{
    protected $output;

    /**
     * Get all logs.
     * `git log --pretty=format:"[%ad][%H][%an]%s" --date=format:"%Y/%m/%d %H:%M:%S" --name-status`
     * @throws GitException
     * @return self
     */
    public function getAllLogs() : array
    {
        return $this->begin()
            ->run(
                'git log ' .
                '--pretty=format:"[%H][%h][%P][%ad][%an]%s"' .
                ' --date=format:"%Y/%m/%d %H:%M:%S" --name-status'
            )->end()
            ->pareseCommitLog();
    }

    public function getAfterLogs(string $afterTime) : array
    {
        return $this->begin()
            ->run(
                'git log ' .
                '--pretty=format:"[%H][%h][%P][%ad][%an]%s"' .
                ' --date=format:"%Y/%m/%d %H:%M:%S" --name-status' .
                " --after=\"$afterTime\""
            )->end()
            ->pareseCommitLog();
    }

    public function getFileLastLog(string $path) : array
    {
        return $this->begin()
            ->run(
                'git log ' .
                '--pretty=format:"[%H][%h][%P][%ad][%an]%s"' .
                ' --date=format:"%Y/%m/%d %H:%M:%S" --name-status' .
                " -1 \"$path\""
            )->end()
            ->pareseCommitLog();
    }

    /**
     * HEADのハッシュ値を取得する
     */
    public function getHeadRev() : string
    {
        return $this->begin()
            ->run(
                'git rev-parse HEAD'
            )->end()
            ->output[0];
    }

    public function getContents($commitId, $path) : string 
    {
        return $this->begin()
            ->run(
                "git show $commitId:$path"
            )->end()
            ->implode();
    }

    /**
     * Runs command.
     * @param  string|array
     * @return self
     * @throws GitException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function run($cmd/*, $options = NULL*/)
    {
        $args = func_get_args();
        $cmd = self::processCommand($args);
        exec($cmd . ' 2>&1', $output, $ret);
        $this->output = $output;
        if ($ret !== 0) {
            throw new \Cz\Git\GitException("Command '$cmd' failed (exit-code $ret).", $ret);
        }
        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function pareseCommitLog() : array
    {
        $commitLogInfos = [];
        foreach ($this->output as $line) {
            if (mb_strpos($line, '[') === 0) {
                $commitLogInfos[] = $this->createCommitLogInfo($line);
            } elseif (mb_strpos($line, "A\t") === 0 ||
                      mb_strpos($line, "M\t") === 0 ||
                      mb_strpos($line, "D\t") === 0 ||
                      (mb_strpos($line, "R") === 0 &&
                       mb_strpos($line, "\t") === 4)) {
                $end = end($commitLogInfos);
                $ope = VerCtrl\OpePathInfo::build($line);
                $end->pushOpePaths($ope);
            }
        }
        return $commitLogInfos;
    }

    /**
     * Runs command.
     * @param  string|array
     * @return self
     * @throws GitException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function runLog($cmd/*, $options = NULL*/)
    {
        $this->commitLogInfos = [];
        $args = func_get_args();
        $cmd = self::processCommand($args);
        exec($cmd . ' 2>&1', $output, $ret);

        if ($ret !== 0) {
            throw new \Cz\Git\GitException("Command '$cmd' failed (exit-code $ret).", $ret);
        }
        return $this;
    }
    protected function createCommitLogInfo($line)
    {
        // コミット行
        $parsedLine = $this->parseLine($line, "]", 5);
        $parentId = $parsedLine[2];
        $parentId2 = '';
        $parentParsed = explode(" ", $parentId);
        if (count($parentParsed) >= 2) {
            $parentId = $parentParsed[0];
            $parentId2 = $parentParsed[1];
        }
        return new VerCtrl\CommitLogInfo(
            $parsedLine[0],
            $parsedLine[1],
            $parentId,
            $parsedLine[3],
            $parsedLine[4],
            $parsedLine[5],
            $parentId2
        );
    }

    protected function parseLine(string $line, string $delimiter, int $count)
    {
        $ret = [];
        $tmp = $line;
        for ($i = 0; $i<$count; $i++) {
            $lastPos = mb_strpos($tmp, $delimiter) - 1;
            $ret[] = mb_substr($tmp, 1, $lastPos);
            $tmp = mb_substr($tmp, $lastPos + 2);
        }
        $ret[] = $tmp;
        return $ret;
    }

    protected function implode() : string
    {
        return implode("\n", $this->output);
    }
}
