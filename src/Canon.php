<?php

declare(strict_types=1);

namespace Ryancco\Canon;

use League\Flysystem\Filesystem;
use Ryancco\Canon\Compilers\NativeCompiler;
use Ryancco\Canon\Contracts\Compiler;

class Canon
{
    public function __construct(
        protected Filesystem $filesystem,
        protected Compiler $compiler = new NativeCompiler()
    ) {
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
        $template = $this->filesystem->fileExists($template)
            ? $this->filesystem->read($template)
            : $template;

        $this->filesystem->write($filename, $this->compiler->compile($template, $data));
    }
}
