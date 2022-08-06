<?php

declare(strict_types=1);

namespace Ryancco\Canon\Compilers;

use Exception;
use Illuminate\View\Compilers\BladeCompiler as Blade;
use Ryancco\Canon\Contracts\Compiler;

class BladeCompiler implements Compiler
{
    public function __construct(protected Blade $blade)
    {
    }

    public function compile(string $template, array $data = []): string
    {
        $generated = $this->blade->compileString($template);

        if (ob_start()) {
            extract($data, EXTR_SKIP);
        }

        try {
            eval('?>'.$generated);
        } catch (Exception $e) {
            ob_get_clean();

            throw $e;
        }

        return (string) ob_get_clean();
    }
}
