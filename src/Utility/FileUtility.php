<?php
namespace codeview\Utility;

class FileUtility
{
    /**
     * リトライ付きでディレクトリを削除する
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function deleteDirectoryRetry(string $dir, int $count) : bool
    {
        if (!@FileUtility::deleteDirectory($dir)) {
            if ($count === 0) {
                return false;
            }
            usleep(200000);
            return FileUtility::deleteDirectoryRetry($dir, $count - 1);
        }
        return false;
    }

    /**
     * ディレクトリを削除する
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function deleteDirectory(string $dir) : bool
    {
        if (!file_exists($dir)) {
            return true;
        }
    
        if (!is_dir($dir)) {
            chmod($dir, '0755');
            return unlink($dir);
        }
    
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
    
            if (!FileUtility::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
    
        return rmdir($dir);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public static function copyDirectory(string $source, string $dest) : void
    {
        mkdir($dest, 0755);
        foreach ($iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $source,
                \RecursiveDirectoryIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        ) as $item) {
            if ($item->isDir()) {
                mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    /**
     * パス区切り文字をOS対応パス文字に置換を行う
     * @param string $path パス文字
     * @return OS対応パス文字に変換
     */
    public static function getOsPath(string $path) : string
    {
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        return $path;
    }
}
