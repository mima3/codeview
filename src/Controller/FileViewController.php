<?php
namespace codeview\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ファイル表示用コントロール
 */
class FileViewController extends ControllerBase
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
        $params = rtrim($args['params'], '/');
        $pathList = explode('/', $params);
        $repositoryId = $pathList[0];
        $repositoryModel = $this->container->get('repositoryModel');
        $dbLog = $repositoryModel->getById($repositoryId);
        array_splice($pathList, 0, 1);
        $path = \codeview\Utility\FileUtility::getOsPath($dbLog->local . "/" . implode($pathList, "/"));
        if (!file_exists($path)) {
            // エラー処理
            $response->getBody()->write("Error not found ". $path);
            return $response;
        }

        $git = new \codeview\VerCtrl\Git\GitRepositoryEx($dbLog->local);
        if (is_dir($path)) {
            // ディレクトリ対処(隠しファイルは覗く)
            $items = glob($path . '/*');
            $files = [];
            foreach ($items as $item) {
                $file = [
                    'name' => basename($item),
                    'is_dir' => is_dir($item),
                    'has_ver' => false
                ];
                $firstLog = $git->getFileLastLog(\codeview\Utility\FileUtility::getOsPath($item));
                if (count($firstLog) > 0) {
                    $file['has_ver'] = true;
                    $file['commit_id'] = $firstLog[0]->getCommitId();
                    $file['commit_short_id'] = $firstLog[0]->getCommitShortId();
                    $file['commit_subject'] = $firstLog[0]->getSubject();
                    $file['commit_date'] = $firstLog[0]->getDate();
                }
                $files[] = $file;
            }
            return $this->view->render(
                $response,
                'fileview_dir.twig',
                [
                    'BASE_PATH' => $this->config['BASE_PATH'],
                    'files' => $files,
                    'params' => $params
                ]
            );
        } else {
            // ファイル操作
            $content = file_get_contents($path);
            if (!$content) {
                // エラー処理
                $response->getBody()->write("Error not found ". $path);
                return $response;
            }
            return $this->view->render(
                $response,
                'fileview_file.twig',
                [
                    'BASE_PATH' => $this->config['BASE_PATH'],
                    'content' => $content,
                    'params' => $params
                ]
            );
        }
    }
}
