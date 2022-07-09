<?php

declare(strict_types=1);

namespace Ryancco\Canon\Compilers;

use Ryancco\Canon\Contracts\Compiler;
use Twig\Environment;

class TwigCompiler implements Compiler
{
    public function __construct(protected Environment $twig)
    {
    }

    public function compile(string $template, array $data = []): string
    {
        return $this->twig->createTemplate($template)->render($data);
    }
}
