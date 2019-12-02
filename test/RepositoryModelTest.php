<?php
namespace codeview\Test;

use \PHPUnit\Framework\TestCase;

/**
 */
class RepositoryModelTest extends TestCase
{
    protected $model;
    protected $database;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp() : void
    {
        \ORM::configure('sqlite::memory:');
        $this->database = \ORM::get_db();
        $this->model = new \codeview\Model\RepositoryModel(null, $this->database);
        $this->model->setup();
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
        $result = $this->model->append(
            'http://hoge/test.git',
            'c:\test\rep1',
            'master',
            'レポジトリA',
            'git',
            '2019/10/15 11:20:35',
            'xxxxxxxxxxxxxxxx'
        );
        $this->assertEquals('1', $result->id);
        $this->assertEquals('http://hoge/test.git', $result->remort);
        $this->assertEquals('c:\test\rep1', $result->local);
        $this->assertEquals('master', $result->branch);
        $this->assertEquals('レポジトリA', $result->name);
        $this->assertEquals('git', $result->type);
        $this->assertEquals('2019/10/15 11:20:35', $result->head_date);
        $this->assertEquals('xxxxxxxxxxxxxxxx', $result->head_id);
        //
        $result = $this->model->append(
            'http://hoge/test2.git',
            'c:\test\rep2',
            'master',
            'レポジトリB',
            'git',
            '2019/10/15 11:20:36',
            'xxxxxxxxxxxxxxx2'
        );
        $this->assertEquals('2', $result->id);
        $this->assertEquals('http://hoge/test2.git', $result->remort);
        $this->assertEquals('c:\test\rep2', $result->local);
        $this->assertEquals('master', $result->branch);
        $this->assertEquals('レポジトリB', $result->name);
        $this->assertEquals('git', $result->type);
        $this->assertEquals('2019/10/15 11:20:36', $result->head_date);
        $this->assertEquals('xxxxxxxxxxxxxxx2', $result->head_id);
        //
        $result = $this->model->get();
        $this->assertEquals('2', count($result));
        $this->assertEquals('1', $result[0]['id']);
        $this->assertEquals('http://hoge/test.git', $result[0]->remort);
        $this->assertEquals('c:\test\rep1', $result[0]->local);
        $this->assertEquals('master', $result[0]->branch);
        $this->assertEquals('レポジトリA', $result[0]->name);
        $this->assertEquals('git', $result[0]->type);
        $this->assertEquals('2019/10/15 11:20:35', $result[0]->head_date);
        $this->assertEquals('xxxxxxxxxxxxxxxx', $result[0]->head_id);
        //
        $this->assertEquals('http://hoge/test2.git', $result[1]->remort);
        $this->assertEquals('c:\test\rep2', $result[1]->local);
        $this->assertEquals('master', $result[1]->branch);
        $this->assertEquals('レポジトリB', $result[1]->name);
        $this->assertEquals('git', $result[1]->type);
        $this->assertEquals('2019/10/15 11:20:36', $result[1]->head_date);
        $this->assertEquals('xxxxxxxxxxxxxxx2', $result[1]->head_id);
    }
}
