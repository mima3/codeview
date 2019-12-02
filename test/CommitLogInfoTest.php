<?php
namespace codeview\Test;

use \PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class CommitLogInfoTest extends TestCase
{
    public function testCompairSame() : void
    {
        $leftList = [];
        $leftList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            'bbcca0a9995',
            'd5ab6b79feded3a4b19f3c00cf44b1e8db705dc9',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットA',
            ''
        );
        $leftList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9997',
            'bbcca0a9997',
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットB',
            '06b17d05b59835201e512038eebb0bbcca0a9990'
        );
        $leftList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9777',
            'bbcca0a9777',
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットC',
            '06b17d05b59835201e512038eebb0bbcca0a9990'
        );

        $rightList = [];
        $rightList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            'bbcca0a9995',
            'd5ab6b79feded3a4b19f3c00cf44b1e8db705dc9',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットA',
            ''
        );
        $rightList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9997',
            'bbcca0a9997',
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットB',
            '06b17d05b59835201e512038eebb0bbcca0a9990'
        );
        $rightList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9777',
            'bbcca0a9777',
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットC',
            '06b17d05b59835201e512038eebb0bbcca0a9990'
        );
        $act = \codeview\VerCtrl\CommitLogInfo::compair($leftList, $rightList);
        $this->assertEquals(0, count($act['appended']));
        $this->assertEquals(0, count($act['deleted']));
    }
    public function testCompairAppended() : void
    {
        $leftList = [];
        $leftList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            'bbcca0a9995',
            'd5ab6b79feded3a4b19f3c00cf44b1e8db705dc9',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットA',
            ''
        );

        $rightList = [];
        $rightList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            'bbcca0a9995',
            'd5ab6b79feded3a4b19f3c00cf44b1e8db705dc9',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットA',
            ''
        );
        $rightList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9997',
            'bbcca0a9997',
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットB',
            '06b17d05b59835201e512038eebb0bbcca0a9990'
        );
        $rightList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9777',
            'bbcca0a9777',
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットC',
            '06b17d05b59835201e512038eebb0bbcca0a9990'
        );
        $act = \codeview\VerCtrl\CommitLogInfo::compair($leftList, $rightList);
        $this->assertEquals(2, count($act['appended']));
        $this->assertEquals('06b17d05b59835201e512038eebb0bbcca0a9997', $act['appended'][0]->getCommitId());
        $this->assertEquals('06b17d05b59835201e512038eebb0bbcca0a9777', $act['appended'][1]->getCommitId());
        $this->assertEquals(0, count($act['deleted']));
    }
    public function testCompairDeleted() : void
    {
        $leftList = [];
        $leftList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            'bbcca0a9995',
            'd5ab6b79feded3a4b19f3c00cf44b1e8db705dc9',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットA',
            ''
        );
        $leftList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9997',
            'bbcca0a9997',
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットB',
            '06b17d05b59835201e512038eebb0bbcca0a9990'
        );
        $leftList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9777',
            'bbcca0a9777',
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットC',
            '06b17d05b59835201e512038eebb0bbcca0a9990'
        );

        $rightList = [];
        $rightList[] = new \codeview\VerCtrl\CommitLogInfo(
            '06b17d05b59835201e512038eebb0bbcca0a9995',
            'bbcca0a9995',
            'd5ab6b79feded3a4b19f3c00cf44b1e8db705dc9',
            '2018/01/05 13:25:34',
            'hoge',
            'コミットA',
            ''
        );

        $act = \codeview\VerCtrl\CommitLogInfo::compair($leftList, $rightList);
        $this->assertEquals(0, count($act['appended']));
        $this->assertEquals(2, count($act['deleted']));
        $this->assertEquals('06b17d05b59835201e512038eebb0bbcca0a9997', $act['deleted'][0]->getCommitId());
        $this->assertEquals('06b17d05b59835201e512038eebb0bbcca0a9777', $act['deleted'][1]->getCommitId());
    }
}
