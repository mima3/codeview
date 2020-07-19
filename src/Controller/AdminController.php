<?php
namespace codeview\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use codeview\VerCtrl\Git;

class AdminController extends ControllerBase
{
    /**
     * レポジトリの追加ページ
     * @param Request $request リクエストオブジェクト
     * @param Response $response レスポンスオブジェクト
     * @param array $args 引数
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addRepositoryPage(Request $request, Response $response, array $args) : Response
    {
        return $this->view->render(
            $response,
            'admin_repository_add.twig',
            [
                'BASE_PATH' => $this->config['BASE_PATH'],
            ]
        );
    }

    /**
     * レポジトリ一覧
     * @param Request $request リクエストオブジェクト
     * @param Response $response レスポンスオブジェクト
     * @param array $args 引数
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function repositoryPage(Request $request, Response $response, array $args) : Response
    {
        $repositoryModel = $this->container->get('repositoryModel');
        $repositories = $repositoryModel->get();
         
        return $this->view->render(
            $response,
            'main.twig',
            [
                'BASE_PATH' => $this->config['BASE_PATH']
            ]
        );
    }

    /**
     * レポジトリの追加処理
     * @param Request $request リクエストオブジェクト
     * @param Response $response レスポンスオブジェクト
     * @param array $args 引数
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function addRepository(Request $request, Response $response, array $args) : Response
    {
        $data = $request->getParsedBody();
        $result = [];
        $repositoryModel = $this->container->get('repositoryModel');
        $commitInfoModel = $this->container->get('commitInfoModel');

        $local = $this->config['REPO_PATH'] . DIRECTORY_SEPARATOR .uniqid('', true);
        try {
            $this->database->beginTransaction();
            $git = Git\GitRepositoryEx::cloneRepository(
                $data['remote'],
                $local,
                ['-b'=>$data['branch']]
            );
            $log = $git->getFileLastLog($local);
            $repRec = $repositoryModel->append(
                $data['remote'],
                $local,
                $data['branch'],
                $data['name'],
                'git',
                $log[0]->getDate(),
                $log[0]->getCommitId()
            );
            $logs = $git->getAllLogs();
            $commitInfoModel->append($repRec['id'], $logs);

            $result['created_id'] = $repRec['id'];
            //
            $this->database->commit();
        } catch (\Cz\Git\GitException | \PDOException | Exception $e) {
            $result['errors'] = $e->getMessage();
            if (file_exists($local)) {
                \codeview\Utility\FileUtility::deleteDirectoryRetry($local, 5);
            }
            $this->database->rollback();
        }

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(201);
    }
    /**
     * レポジトリの削除処理
     * @param Request $request リクエストオブジェクト
     * @param Response $response レスポンスオブジェクト
     * @param array $args 引数
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function deleteRepository(Request $request, Response $response, array $args) : Response
    {
        $result = [];
        $repositoryId = $args['id'];
        $repositoryModel = $this->container->get('repositoryModel');
        $commitInfoModel = $this->container->get('commitInfoModel');

        try {
            $this->database->beginTransaction();
            $rec = $repositoryModel->getById($repositoryId);
            $commitInfoModel->deleteByRepositoryId($repositoryId);
            $repositoryModel->delete($repositoryId);
            \codeview\Utility\FileUtility::deleteDirectoryRetry($rec->local, 5);
            $this->database->commit();
        } catch (\Cz\Git\GitException | \PDOException | Exception $e) {
            $result['errors'] = $e->getMessage();
            $this->database->rollback();
        }

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(200);
    }
}
