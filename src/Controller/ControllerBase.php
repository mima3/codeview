<?php
namespace codeview\Controller;

use Psr\Container\ContainerInterface;

class ControllerBase
{
    protected $container;
    protected $view;
    protected $config;
    protected $database;

    /**
     * コンストラクタ
     * @param ContainerInterface $container コンテナ
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->view = $this->container->get('view');
        $this->config = $this->container->get('config');
        $this->database = $this->container->get('database');
    }
}
