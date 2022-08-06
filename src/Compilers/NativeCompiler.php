<?php

declare(strict_types=1);

namespace Ryancco\Canon\Compilers;

use InvalidArgumentException;
use Ryancco\Canon\Contracts\Compiler;

class NativeCompiler implements Compiler
{
    public function compile(string $template, array $data = []): string
    {
        $replace = [];

        foreach ($data as $key => $value) {
            if (is_array($value) || (is_object($value) && ! method_exists($value, '__toString'))) {
                throw new InvalidArgumentException('Invalid value provided for key '.$key);
            }

            $replace['{ '.$key.' }'] = $value;
        }

        return strtr($template, $replace);
    }
}
