<?php
namespace codeview\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ファイル表示用
 */
class FileController extends ControllerBase
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

        $repository = $repositoryModel->getById($repositoryId);
        $git = new \codeview\VerCtrl\Git\GitRepositoryEx($repository->local);
        $contents = "";
        try {
            $contents = $git->getContents($commitId, $paths);
        } catch(\Cz\Git\GitException $e) {
            $contents = "";
        }
        $jsondata = json_encode(
            [
                'contents' => $contents,
                'paths' => $paths,
                'repositoryId' => $repositoryId,
                'commitId' => $commitId,
            ]
        );
        $response->getBody()->write($jsondata);
        return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(200);
    }
}
