<?php
namespace codeview\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * コミットログ表示用コントロール
 */
class CommitLogController extends ControllerBase
{
    /**
     * コミットログの表示
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
            'commitlog.twig',
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
}
