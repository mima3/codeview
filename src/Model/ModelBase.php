<?php
namespace codeview\Model;

abstract class ModelBase
{
    protected $database;
    protected $app;
    public function __construct($app, $database)
    {
        $this->app = $app;
        $this->database = $database;
    }

    public function __destruct()
    {
        $this->app = null;
        $this->database = null;
    }
    abstract public function setup();
}
