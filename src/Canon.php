<?php

declare(strict_types=1);

namespace Ryancco\Canon;

use League\Flysystem\Filesystem;
use Ryancco\Canon\Compilers\TwigCompiler;
use Ryancco\Canon\Contracts\Compiler;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class Canon
{
    public function __construct(
        protected Filesystem $inputFilesystem,
        protected ?Compiler $compiler = null,
        protected ?Filesystem $outputFilesystem = null
    ) {
        if ($this->compiler === null) {
            $this->compiler = new TwigCompiler(new Environment(new ArrayLoader([])));
        }

        if ($this->outputFilesystem === null) {
            $this->outputFilesystem = $this->inputFilesystem;
        }
    }

    /**
     * Generate a file from a template file or string.
     *
     * @param string $template
     * @param string $filename
     * @param array $data
     *
     * @return void
     * @throws \League\Flysystem\FilesystemException
     */
    public function generate(string $template, string $filename, array $data = []): void
    {
        $template = $this->inputFilesystem->fileExists($template)
            ? $this->inputFilesystem->read($template)
            : $template;

        $this->outputFilesystem->write($filename, $this->compiler->compile($template, $data));
    }
}
