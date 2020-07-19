<?php
namespace codeview\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use codeview\VerCtrl\Git;

/**
 * コミットログ表示用コントロール
 */
class CommitLogController extends ControllerBase
{
    /**
     * コミットログを表示
     * @param Request $request リクエストオブジェクト
     * @param Response $response レスポンスオブジェクト
     * @param array $args 引数
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function view(Request $request, Response $response, array $args) : Response
    {
        $repositoryId = $args['repository_id'];
        $commitId = $args['commit_id'];

        $commitInfoModel = $this->container->get('commitInfoModel');
        $commitlogs = $commitInfoModel->getByCommitId($repositoryId, $commitId, true);
        return $this->view->render(
            $response,
            'commitlog.twig',
            [
                'BASE_PATH' => $this->config['BASE_PATH'],
                'repositoryId' => $repositoryId,
                'commitlogs' => $commitlogs
            ]
        );
    }

    /**
     * コミットログの一覧を表示
     * @param Request $request リクエストオブジェクト
     * @param Response $response レスポンスオブジェクト
     * @param array $args 引数
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function list(Request $request, Response $response, array $args) : Response
    {
        $repositoryId = $args['repository_id'];
        $commitInfoModel = $this->container->get('commitInfoModel');
        $count = $commitInfoModel->count($repositoryId);

        $params = $request->getQueryParams();
        $limit = $this->config['COMMIT_LOG_PAGE_PER_LIMIT'];
        $offset = 0;
        $page = 1;

        if (array_key_exists('page', $params)) {
            $page = $params['page'];
        }
        $maxPage = ceil($count / $limit);
        if ($page > 1) {
            $offset = (($page - 1) * $limit);
        }


        $commitlogs = $commitInfoModel->getPage($repositoryId, $offset, $limit, true);
        return $this->view->render(
            $response,
            'commitlog_list.twig',
            [
                'BASE_PATH' => $this->config['BASE_PATH'],
                'repositoryId' => $repositoryId,
                'commitlogs' => $commitlogs,
                'pageLimit' => $limit,
                'currentPage' => $page,
                'maxPage' => $maxPage,
                'pageRange' => 2
            ]
        );
    }

    /**
     * コミットログを更新
     * @param Request $request リクエストオブジェクト
     * @param Response $response レスポンスオブジェクト
     * @param array $args 引数
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function update(Request $request, Response $response, array $args) : Response
    {
        $repositoryId = $args['repository_id'];
        $repositoryModel = $this->container->get('repositoryModel');
        $commitInfoModel = $this->container->get('commitInfoModel');

        $rep = $repositoryModel->getById($repositoryId);
        $updated = false;
        $result = [
            'updated' => false,
            'errors' => ""
        ];
        try {
            $this->database->beginTransaction();
            $git = new Git\GitRepositoryEx(
                $rep->local
            );
            $beforeHead = $rep->head_id;
            $git->pull();
            $afterHead = $git->getHeadRev();
            if ($beforeHead != $afterHead) {
                $result['updated'] = true;
                $commitlogs = $git->getAfterLogs($rep->head_date);
                array_splice($commitlogs, count($commitlogs) - 1, 1);
                $commitInfoModel->append($repositoryId, $commitlogs);

                $repositoryModel->update($repositoryId, $commitlogs[0]->getCommitId(), $commitlogs[0]->getDate());
            }

            //
            $this->database->commit();
        } catch (\Cz\Git\GitException | \PDOException | Exception $e) {
            $result['errors'] = $e->getMessage();
            $this->database->rollback();
        }
        $response->getBody()->write(json_encode($result));
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);
    }
}
