<?php
namespace codeview\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ファイル差分表示用
 */
class FileDiffController extends ControllerBase
{
    /**
     * ファイルの表示
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
        $paths = $args['paths'];
        $commitId = $args['commit_id'];
        $repositoryId = $args['repository_id'];

        $repositoryModel = $this->container->get('repositoryModel');
        $commitInfoModel = $this->container->get('commitInfoModel');

        $repository = $repositoryModel->getById($repositoryId);
        $git = new \codeview\VerCtrl\Git\GitRepositoryEx($repository->local);
        $commit = $commitInfoModel->getByCommitId($repositoryId, $commitId, false);
        $afterContents = $git->getContents($commitId, $paths);
        $beforeContents = $git->getContents($commit[0]->getParentId(), $paths);
        return $this->view->render(
            $response,
            'filediff.twig',
            [
                'BASE_PATH' => $this->config['BASE_PATH'],
                'repositoryId' => $repositoryId,
                'beforeContents' => $beforeContents,
                'beforeCommitId' => $commit[0]->getParentId(),
                'afterContents' => $afterContents,
                'afterCommitId' => $commitId
            ]
        );
    }
}
