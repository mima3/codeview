<?php
namespace codeview\Test;

use \PHPUnit\Framework\TestCase;
use \codeview\VerCtrl\Git as Git;
use \codeview\Utility as Utility;

/**
 */
class GitRepositoryExTest extends TestCase
{
    protected $ctrl;
    protected $repositoryPath;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp() : void
    {
        $this->repositoryPath = dirname(__FILE__) . '/tmp';
        if (file_exists($this->repositoryPath)) {
            Utility\FileUtility::deleteDirectoryRetry($this->repositoryPath, 10);
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function tearDown() : void
    {
        if (file_exists($this->repositoryPath)) {
            Utility\FileUtility::deleteDirectoryRetry($this->repositoryPath, 10);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testLog001()
    {
        // 事前準備
        $this->ctrl = Git\GitRepositoryEx::cloneRepository(
            dirname(__FILE__) . '/testdata/git_template/test001',
            $this->repositoryPath,
            ['-b'=>'master']
        );

        // テスト開始
        $act = $this->ctrl->getAllLogs();
        $this->assertEquals(4, count($act));
        $this->assertEquals('ff7fd06d6c6d522dff7919bfaec114df8ec977ff', $act[3]->getCommitId());
        $this->assertEquals('初回コミット', $act[3]->getSubject());
        $this->assertEquals('', $act[3]->getParentId());
        $this->assertEquals('', $act[3]->getParentId2());
        $this->assertEquals('2019/11/22 19:26:33', $act[3]->getDate());
        $this->assertEquals('mima3', $act[3]->getAuthor());
        $this->assertEquals(2, count($act[3]->getOpePaths()));
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_ADD, $act[3]->getOpePaths()[0]->getOpe());
        $this->assertEquals('aaa.txt', $act[3]->getOpePaths()[0]->getPath());
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_ADD, $act[3]->getOpePaths()[1]->getOpe());
        $this->assertEquals('bbb.txt', $act[3]->getOpePaths()[1]->getPath());

        $this->assertEquals('0ab5ce1aed568ef5ca6584b5a268bf84db384c51', $act[2]->getCommitId());
        $this->assertEquals('更新とフォルダ追加', $act[2]->getSubject());
        $this->assertEquals('ff7fd06d6c6d522dff7919bfaec114df8ec977ff', $act[2]->getParentId());
        $this->assertEquals('', $act[2]->getParentId2());
        $this->assertEquals('2019/11/22 19:28:05', $act[2]->getDate());
        $this->assertEquals('mima3', $act[2]->getAuthor());
        $this->assertEquals(2, count($act[2]->getOpePaths()));
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_MODIFY, $act[2]->getOpePaths()[0]->getOpe());
        $this->assertEquals('aaa.txt', $act[2]->getOpePaths()[0]->getPath());
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_ADD, $act[2]->getOpePaths()[1]->getOpe());
        $this->assertEquals('test/ccc.txt', $act[2]->getOpePaths()[1]->getPath());

        $this->assertEquals('da34f05117b47356d5bbdfb03a5a210ebccafd56', $act[1]->getCommitId());
        $this->assertEquals('改名と移動', $act[1]->getSubject());
        $this->assertEquals('0ab5ce1aed568ef5ca6584b5a268bf84db384c51', $act[1]->getParentId());
        $this->assertEquals('', $act[1]->getParentId2());
        $this->assertEquals('2019/11/22 19:29:03', $act[1]->getDate());
        $this->assertEquals('mima3', $act[1]->getAuthor());
        $this->assertEquals(2, count($act[1]->getOpePaths()));
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_MOVE, $act[1]->getOpePaths()[0]->getOpe());
        $this->assertEquals('aaa.txt', $act[1]->getOpePaths()[0]->getPath());
        $this->assertEquals('aaa_renamed.txt', $act[1]->getOpePaths()[0]->getPath2());
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_MOVE, $act[1]->getOpePaths()[1]->getOpe());
        $this->assertEquals('bbb.txt', $act[1]->getOpePaths()[1]->getPath());
        $this->assertEquals('test/bbb.txt', $act[1]->getOpePaths()[1]->getPath2());

        $this->assertEquals('f5941f1bd04688e88f41818d1a16301f5064c056', $act[0]->getCommitId());
        $this->assertEquals('削除', $act[0]->getSubject());
        $this->assertEquals('da34f05117b47356d5bbdfb03a5a210ebccafd56', $act[0]->getParentId());
        $this->assertEquals('', $act[0]->getParentId2());
        $this->assertEquals('2019/11/22 19:29:37', $act[0]->getDate());
        $this->assertEquals('mima3', $act[0]->getAuthor());
        $this->assertEquals(1, count($act[0]->getOpePaths()));
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_DELTE, $act[0]->getOpePaths()[0]->getOpe());
        $this->assertEquals('test/ccc.txt', $act[0]->getOpePaths()[0]->getPath());
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testLog002()
    {
        // 事前準備
        $this->ctrl = Git\GitRepositoryEx::cloneRepository(
            dirname(__FILE__) . '/testdata/git_template/test001',
            $this->repositoryPath,
            ['-b'=>'master']
        );

        // テスト開始
        $act = $this->ctrl->getAfterLogs("2019/11/22 19:29:00");
        $this->assertEquals(2, count($act));
        $this->assertEquals('da34f05117b47356d5bbdfb03a5a210ebccafd56', $act[1]->getCommitId());
        $this->assertEquals('改名と移動', $act[1]->getSubject());
        $this->assertEquals('0ab5ce1aed568ef5ca6584b5a268bf84db384c51', $act[1]->getParentId());
        $this->assertEquals('', $act[1]->getParentId2());
        $this->assertEquals('2019/11/22 19:29:03', $act[1]->getDate());
        $this->assertEquals('mima3', $act[1]->getAuthor());
        $this->assertEquals(2, count($act[1]->getOpePaths()));
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_MOVE, $act[1]->getOpePaths()[0]->getOpe());
        $this->assertEquals('aaa.txt', $act[1]->getOpePaths()[0]->getPath());
        $this->assertEquals('aaa_renamed.txt', $act[1]->getOpePaths()[0]->getPath2());
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_MOVE, $act[1]->getOpePaths()[1]->getOpe());
        $this->assertEquals('bbb.txt', $act[1]->getOpePaths()[1]->getPath());
        $this->assertEquals('test/bbb.txt', $act[1]->getOpePaths()[1]->getPath2());

        $this->assertEquals('f5941f1bd04688e88f41818d1a16301f5064c056', $act[0]->getCommitId());
        $this->assertEquals('削除', $act[0]->getSubject());
        $this->assertEquals('da34f05117b47356d5bbdfb03a5a210ebccafd56', $act[0]->getParentId());
        $this->assertEquals('', $act[0]->getParentId2());
        $this->assertEquals('2019/11/22 19:29:37', $act[0]->getDate());
        $this->assertEquals('mima3', $act[0]->getAuthor());
        $this->assertEquals(1, count($act[0]->getOpePaths()));
        $this->assertEquals(\codeview\VerCtrl\OpePathInfo::OPE_DELTE, $act[0]->getOpePaths()[0]->getOpe());
        $this->assertEquals('test/ccc.txt', $act[0]->getOpePaths()[0]->getPath());
    }
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testLog03()
    {
        // 事前準備
        $this->ctrl = Git\GitRepositoryEx::cloneRepository(
            dirname(__FILE__) . '/testdata/git_template/test002',
            $this->repositoryPath,
            ['-b'=>'master']
        );
        // テスト開始
        $act = $this->ctrl->getAllLogs();
        $this->assertEquals(5, count($act));
        $this->assertEquals('9043d85b752bcf384274357220eb59eb7de448ff', $act[4]->getCommitId());
        $this->assertEquals('', $act[4]->getParentId());
        $this->assertEquals('', $act[4]->getParentId2());
        $this->assertEquals('3ffddfc9992f16168541ff19221c9994a698bb85', $act[3]->getCommitId());
        $this->assertEquals('9043d85b752bcf384274357220eb59eb7de448ff', $act[3]->getParentId());
        $this->assertEquals('', $act[3]->getParentId2());
        $this->assertEquals('1307a75a3ba6425a6244d6a765b5651cf8247a17', $act[2]->getCommitId());
        $this->assertEquals('9043d85b752bcf384274357220eb59eb7de448ff', $act[2]->getParentId());
        $this->assertEquals('', $act[2]->getParentId2());
        $this->assertEquals('cc494aa3af0c1d3778fe4bbb17d8e2c80fdb1078', $act[1]->getCommitId());
        $this->assertEquals('1307a75a3ba6425a6244d6a765b5651cf8247a17', $act[1]->getParentId());
        $this->assertEquals('3ffddfc9992f16168541ff19221c9994a698bb85', $act[1]->getParentId2());
        $this->assertEquals('7e28ca030fef0c4c4e8a97f3999d59d5443e02d1', $act[0]->getCommitId());
        $this->assertEquals('cc494aa3af0c1d3778fe4bbb17d8e2c80fdb1078', $act[0]->getParentId());
        $this->assertEquals('', $act[0]->getParentId2());
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testLog04()
    {
        // 事前準備
        $this->ctrl = Git\GitRepositoryEx::cloneRepository(
            dirname(__FILE__) . '/testdata/git_template/test002',
            $this->repositoryPath,
            ['-b'=>'branch1']
        );
        // テスト開始
        $act = $this->ctrl->getAllLogs();
        $this->assertEquals(3, count($act));
        $this->assertEquals('9043d85b752bcf384274357220eb59eb7de448ff', $act[2]->getCommitId());
        $this->assertEquals('', $act[2]->getParentId());
        $this->assertEquals('', $act[2]->getParentId2());
        $this->assertEquals('3ffddfc9992f16168541ff19221c9994a698bb85', $act[1]->getCommitId());
        $this->assertEquals('9043d85b752bcf384274357220eb59eb7de448ff', $act[1]->getParentId());
        $this->assertEquals('', $act[1]->getParentId2());
        $this->assertEquals('1d94d4f75ac08cee743dfb2dc2b2830baa10da08', $act[0]->getCommitId());
        $this->assertEquals('3ffddfc9992f16168541ff19221c9994a698bb85', $act[0]->getParentId());
        $this->assertEquals('', $act[0]->getParentId2());
    }
   /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testHeadRev01()
    {
        // 事前準備
        $this->ctrl = Git\GitRepositoryEx::cloneRepository(
            dirname(__FILE__) . '/testdata/git_template/test001',
            $this->repositoryPath,
            ['-b'=>'master']
        );

        // テスト開始
        $act = $this->ctrl->getHeadRev();
        $this->assertEquals('f5941f1bd04688e88f41818d1a16301f5064c056', $act);
    }
}
