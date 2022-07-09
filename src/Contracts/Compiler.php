<?php

declare(strict_types=1);

namespace Ryancco\Canon\Contracts;

interface Compiler
{
    public function compile(string $template, array $data = []): string;
}
