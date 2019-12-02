<?php
namespace codeview\Test;

use \PHPUnit\Framework\TestCase;

/**
 */
class CommitModelTest extends TestCase
{
    protected $commitModel;
    protected $database;
    protected $repModel;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp() : void
    {
        \ORM::configure('sqlite::memory:');
        $this->database = \ORM::get_db();
        $this->repModel = new \codeview\Model\RepositoryModel(null, $this->database);
        $this->repModel->setup();
        $this->repModel->append(
            'http://hoge/test.git',
            'c:\test\rep1',
            'master',
            'レポジトリA',
            'git',
            '2019/10/15 11:20:35',
            'xxxxxxxxxxxxxxxx'
        );
        $this->repModel->append(
            'http://hoge/test2.git',
            'c:\test\rep2',
            'master',
            'レポジトリB',
            'git',
            '2019/10/15 11:20:36',
            'xxxxxxxxxxxxxxxx'
        );
        $this->commitModel = new \codeview\Model\CommitInfoModel(null, $this->database);
        $this->commitModel->setup();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function tearDown() : void
    {
        \ORM::reset_db();
    }

    public function testAppend()
    {
        {
            $commitlist = [];
            {
                $obj = new \codeview\VerCtrl\CommitLogInfo(
                    '06b17d05b59835201e512038eebb0bbcca0a9995',
                    'bbcca0a9995',
                    'd5ab6b79feded3a4b19f3c00cf44b1e8db705dc9',
                    '2018/01/05 13:25:34',
                    'hoge',
                    'コミットA',
                    ''
                );
                $obj->pushOpePaths(
                    new \codeview\VerCtrl\OpePathInfo(
                        \codeview\VerCtrl\OpePathInfo::OPE_MOVE,
                        '/test/from/test.php',
                        '/test/to/test.php'
                    )
                );
                $obj->pushOpePaths(
                    new \codeview\VerCtrl\OpePathInfo(
                        \codeview\VerCtrl\OpePathInfo::OPE_MODIFY,
                        '/test/model/test2.php'
                    )
                );
                $obj->pushOpePaths(
                    new \codeview\VerCtrl\OpePathInfo(
                        \codeview\VerCtrl\OpePathInfo::OPE_MODIFY,
                        '/test/model/test3.php'
                    )
                );
                $commitlist[] = $obj;
            }

            {
                $obj = new \codeview\VerCtrl\CommitLogInfo(
                    '06b17d05b59835201e512038eebb0bbcca0a9997',
                    'bbcca0a9997',
                    '06b17d05b59835201e512038eebb0bbcca0a9995',
                    '2018/01/05 13:25:34',
                    'hoge',
                    'コミットB',
                    '06b17d05b59835201e512038eebb0bbcca0a9990'
                );
                $obj->pushOpePaths(
                    new \codeview\VerCtrl\OpePathInfo(
                        \codeview\VerCtrl\OpePathInfo::OPE_MODIFY,
                        '/test/model/xxxx.php'
                    )
                );
                $commitlist[] = $obj;
            }

            $this->commitModel->append(1, $commitlist);
        }
        $act = $this->commitModel->get(1);
        $this->assertEquals(2, count($act));
        $this->assertEquals('06b17d05b59835201e512038eebb0bbcca0a9995', $act[0]->getCommitId());
        $this->assertEquals('bbcca0a9995', $act[0]->getCommitShortId());
        $this->assertEquals('コミットA', $act[0]->getSubject());
        $this->assertEquals('d5ab6b79feded3a4b19f3c00cf44b1e8db705dc9', $act[0]->getParentId());
        $this->assertEquals('', $act[0]->getParentId2());
        $this->assertEquals('2018/01/05 13:25:34', $act[0]->getDate());
        $this->assertEquals('hoge', $act[0]->getAuthor());
        $this->assertEquals(3, count($act[0]->getOpePaths()));
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_MOVE, $act[0]->getOpePaths()[0]->getOpe());
        $this->assertEquals('/test/from/test.php', $act[0]->getOpePaths()[0]->getPath());
        $this->assertEquals('/test/to/test.php', $act[0]->getOpePaths()[0]->getPath2());
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_MODIFY, $act[0]->getOpePaths()[1]->getOpe());
        $this->assertEquals('/test/model/test2.php', $act[0]->getOpePaths()[1]->getPath());
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_MODIFY, $act[0]->getOpePaths()[2]->getOpe());
        $this->assertEquals('/test/model/test3.php', $act[0]->getOpePaths()[2]->getPath());

        $this->assertEquals('06b17d05b59835201e512038eebb0bbcca0a9997', $act[1]->getCommitId());
        $this->assertEquals('bbcca0a9997', $act[1]->getCommitShortId());
        $this->assertEquals('コミットB', $act[1]->getSubject());
        $this->assertEquals('06b17d05b59835201e512038eebb0bbcca0a9995', $act[1]->getParentId());
        $this->assertEquals('06b17d05b59835201e512038eebb0bbcca0a9990', $act[1]->getParentId2());
        $this->assertEquals('2018/01/05 13:25:34', $act[1]->getDate());
        $this->assertEquals('hoge', $act[1]->getAuthor());
        $this->assertEquals(1, count($act[1]->getOpePaths()));
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_MODIFY, $act[1]->getOpePaths()[0]->getOpe());
        $this->assertEquals('/test/model/xxxx.php', $act[1]->getOpePaths()[0]->getPath());
        {
            $commitlist = [];
            {
                $obj = new \codeview\VerCtrl\CommitLogInfo(
                    '06b17d05b59835201e512038eebb0bbcca0aaaaa',
                    'bbcca0aaaaa',
                    '',
                    '2018/01/05 13:25:34',
                    'hoge',
                    'コミットC',
                    '06b17d05b59835201e512038eebb0bbcca0a9990'
                );
                $obj->pushOpePaths(
                    new \codeview\VerCtrl\OpePathInfo(
                        \codeview\VerCtrl\OpePathInfo::OPE_MODIFY,
                        '/test/model/zzzz.php'
                    )
                );
                $commitlist[] = $obj;
            }
            $this->commitModel->append(1, $commitlist);
        }
        $act = $this->commitModel->get(1);
        $this->assertEquals(3, count($act));
        $this->assertEquals('06b17d05b59835201e512038eebb0bbcca0aaaaa', $act[0]->getCommitId());
        $this->assertEquals('06b17d05b59835201e512038eebb0bbcca0a9995', $act[1]->getCommitId());
        $this->assertEquals('06b17d05b59835201e512038eebb0bbcca0a9997', $act[2]->getCommitId());
    }

    public function testDeleteCommit()
    {
        $commitlist = [];
        {
            $obj = new \codeview\VerCtrl\CommitLogInfo(
                '06b17d05b59835201e512038eebb0bbcca0a9995',
                'bbcca0a9995',
                'd5ab6b79feded3a4b19f3c00cf44b1e8db705dc9',
                '2018/01/05 13:25:34',
                'hoge',
                'コミットA',
                ''
            );
            $obj->pushOpePaths(
                new \codeview\VerCtrl\OpePathInfo(
                    \codeview\VerCtrl\OpePathInfo::OPE_MODIFY,
                    '/test/model/test.php'
                )
            );
            $obj->pushOpePaths(
                new \codeview\VerCtrl\OpePathInfo(
                    \codeview\VerCtrl\OpePathInfo::OPE_MODIFY,
                    '/test/model/test2.php'
                )
            );
            $obj->pushOpePaths(
                new \codeview\VerCtrl\OpePathInfo(
                    \codeview\VerCtrl\OpePathInfo::OPE_MODIFY,
                    '/test/model/test3.php'
                )
            );
            $commitlist[] = $obj;
        }
        {
            $obj = new \codeview\VerCtrl\CommitLogInfo(
                '06b17d05b59835201e512038eebb0bbcca0a9997',
                'bbcca0a9997',
                '06b17d05b59835201e512038eebb0bbcca0a9995',
                '2018/01/05 13:25:34',
                'hoge',
                'コミットB',
                '06b17d05b59835201e512038eebb0bbcca0a9990'
            );
            $obj->pushOpePaths(
                new \codeview\VerCtrl\OpePathInfo(
                    \codeview\VerCtrl\OpePathInfo::OPE_MODIFY,
                    '/test/model/xxxx.php'
                )
            );
            $commitlist[] = $obj;
        }
        $this->commitModel->append(1, $commitlist);
        $act = $this->commitModel->get(1);
        $this->assertEquals(2, count($act));

        $this->commitModel->deleteByCommitId(1, '06b17d05b59835201e512038eebb0bbcca0a9995');
        $act = $this->commitModel->get(1);
        $this->assertEquals(1, count($act));
        $this->assertEquals('06b17d05b59835201e512038eebb0bbcca0a9997', $act[0]->getCommitId());
    }

    public function testDeleteRepository()
    {
        {
            $commitlist = [];
            {
                $obj = new \codeview\VerCtrl\CommitLogInfo(
                    '06b17d05b59835201e512038eebb0bbcca0a9995',
                    'bb0bbcca0a9995',
                    'd5ab6b79feded3a4b19f3c00cf44b1e8db705dc9',
                    '2018/01/05 13:25:34',
                    'hoge',
                    'コミットA',
                    ''
                );
                $obj->pushOpePaths(
                    new \codeview\VerCtrl\OpePathInfo(
                        \codeview\VerCtrl\OpePathInfo::OPE_MODIFY,
                        '/test/model/test.php'
                    )
                );
                $obj->pushOpePaths(
                    new \codeview\VerCtrl\OpePathInfo(
                        \codeview\VerCtrl\OpePathInfo::OPE_MODIFY,
                        '/test/model/test2.php'
                    )
                );
                $obj->pushOpePaths(
                    new \codeview\VerCtrl\OpePathInfo(
                        \codeview\VerCtrl\OpePathInfo::OPE_MODIFY,
                        '/test/model/test3.php'
                    )
                );
                $commitlist[] = $obj;
            }
            {
                $obj = new \codeview\VerCtrl\CommitLogInfo(
                    '06b17d05b59835201e512038eebb0bbcca0a9997',
                    'bbcca0a9997',
                    '06b17d05b59835201e512038eebb0bbcca0a9995',
                    '2018/01/05 13:25:34',
                    'hoge',
                    'コミットB',
                    '06b17d05b59835201e512038eebb0bbcca0a9990'
                );
                $obj->pushOpePaths(
                    new \codeview\VerCtrl\OpePathInfo(
                        \codeview\VerCtrl\OpePathInfo::OPE_MODIFY,
                        '/test/model/xxxx.php'
                    )
                );
                $commitlist[] = $obj;
            }
            $this->commitModel->append(1, $commitlist);
        }
        {
            $commitlist = [];
            {
                $obj = new \codeview\VerCtrl\CommitLogInfo(
                    'de462a23947aaaf923619dea9e791a6cc61e3cc1',
                    'a6cc61e3cc1',
                    'd5ab6b79feded3a4b19f3c00cf44b1e8db705dc9',
                    '2018/01/05 13:25:34',
                    'hoge',
                    'コミットA',
                    ''
                );
                $commitlist[] = $obj;
            }
            $this->commitModel->append(2, $commitlist);
        }

        $act = $this->commitModel->get(1);
        $this->assertEquals(2, count($act));

        $act = $this->commitModel->get(2);
        $this->assertEquals(1, count($act));

        $this->commitModel->deleteByRepositoryId(1);
        $act = $this->commitModel->get(1);
        $this->assertEquals(0, count($act));

        $act = $this->commitModel->get(2);
        $this->assertEquals(1, count($act));
    }
}
