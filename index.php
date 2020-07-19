<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteContext;

require_once 'vendor/autoload.php';
require_once './config.php';

$container = new \DI\Container();

// 依存関係コンテナを提供する必要はありません。
// ただし、その場合は、AppFactoryでappを作成する前にコンテナのインスタンスを提供する必要があります
AppFactory::setContainer($container);

$app = AppFactory::create();
$app->setBasePath(BASE_PATH);

if (DEBUG) {
    // If you are adding the pre-packaged ErrorMiddleware set `displayErrorDetails` to `true`
    $app->addErrorMiddleware(true, true, true);
} else {
    // 本番環境ではエラーを表示しない
    // If you are adding the pre-packaged ErrorMiddleware set `displayErrorDetails` to `false`
    $app->addErrorMiddleware(false, true, true);
}
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();


// View
$container->set('view', function () {
    //$view = new \Slim\Views\Twig(TEMPLATE_PATH, ['cache' => VIEW_CACHE_PATH]);
    $view = new \codeview\Extention\TwigEx(TEMPLATE_PATH);
    $view->setup();
    return $view;
});

// Model
$existDb = file_exists(DB_PATH);
\ORM::configure('sqlite:' . DB_PATH);
$database = \ORM::get_db();
$repositoryModel = new \codeview\Model\RepositoryModel($app, $database);
$commitInfoModel = new \codeview\Model\CommitInfoModel($app, $database);

if (!$existDb) {
    $repositoryModel->setup();
    $commitInfoModel->setup();
}

$container->set('database', function () use ($database) {
    return $database;
});
$container->set('repositoryModel', function () use ($repositoryModel) {
    return $repositoryModel;
});
$container->set('commitInfoModel', function () use ($commitInfoModel) {
    return $commitInfoModel;
});
$container->set('config', function () use ($commitInfoModel) {
    $config = [
        'REPO_PATH' => REPO_PATH,
        'BASE_PATH' => BASE_PATH,
        'COMMIT_LOG_PAGE_PER_LIMIT' => COMMIT_LOG_PAGE_PER_LIMIT
    ];
    return $config;
});

// Controller
$app->get('/hoge/{name}', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!". $args['name']);
    return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $view = $this->get('view');
    return $view->render($response, "admin.twig", $args);
});

// 管理者ページ
$app->get('/admin/repository/', \codeview\Controller\AdminController::class . ':repositoryPage');
$app->get('/admin/repository/addpage', \codeview\Controller\AdminController::class . ':addRepositoryPage');
$app->post('/admin/repository/add', \codeview\Controller\AdminController::class . ':addRepository');
$app->post('/admin/repository/delete/{id}', \codeview\Controller\AdminController::class . ':deleteRepository');

//
$app->get('/fileview[/{params:.*}]', \codeview\Controller\FileViewController::class . ':view');
$app->get('/commitlog/{repository_id}', \codeview\Controller\CommitLogController::class . ':list');
$app->post('/commitlog/{repository_id}/update', \codeview\Controller\CommitLogController::class . ':update');
$app->get('/commitlog/{repository_id}/{commit_id}', \codeview\Controller\CommitLogController::class . ':view');
$app->get('/filediff/{repository_id}/{commit_id}[/{paths:.*}]', \codeview\Controller\FileDiffController::class . ':view');
$app->get('/file/{repository_id}/{commit_id}[/{paths:.*}]', \codeview\Controller\FileController::class . ':view');

$app->run();
