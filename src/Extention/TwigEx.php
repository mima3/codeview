<?php
namespace codeview\Extention;

class TwigEx extends \Slim\Views\Twig
{
    /**
     * 拡張用の関数追加
     */
    public function setup() : void
    {
        //
        $baseNameFunc = new \Twig\TwigFunction('basename', function ($path) {
            return basename($path);
        });
        $this->environment->addFunction($baseNameFunc);
    }
}
