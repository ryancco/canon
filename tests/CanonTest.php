<?php

declare(strict_types=1);

namespace Ryancco\Canon\Tests;

use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Ryancco\Canon\Canon;
use Ryancco\Canon\Compilers\TwigCompiler;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class CanonTest extends TestCase
{
    protected function setUp(): void
    {
        if (! file_exists($this->path('/'))) {
            mkdir($this->path('/'), 0755, true);
        }
    }

    public function test_it_generates_a_file_from_a_template_file(): void
    {
        file_put_contents($this->path('hello-world.tpl'), 'Hello, {{ name }}.');
        $this->getCanonInstance()->generate('hello-world.tpl', 'hello-world', ['name' => 'World']);
        $this->assertFileExists($this->path('hello-world'));
    }

    public function test_it_generates_a_file_from_a_template_string(): void
    {
        $this->getCanonInstance()->generate('Hello, {{ name }}.', 'hello-world', ['name' => 'World']);
        $this->assertFileExists($this->path('hello-world'));
    }

    public function test_it_overwrites_a_file(): void
    {
        @unlink($this->path('existing-file'));
        touch($this->path('existing-file'));
        $this->getCanonInstance()->generate('Hello, {{ name }}.', 'existing-file', ['name' => 'World']);
        $this->assertStringStartsWith('Hello', file_get_contents($this->path('existing-file')));
    }

    public function test_it_replaces_variables(): void
    {
        $this->getCanonInstance()->generate('Hello, {{ name }}.', 'hello-world', ['name' => 'World']);
        $this->assertEquals('Hello, World.', file_get_contents($this->path('hello-world')));
    }

    public function test_it_generates_a_file_to_a_separate_output_filesystem(): void
    {
        $this->getCanonInstance($filesystem = new Filesystem(new InMemoryFilesystemAdapter()))->generate('Hello, {{ name }}.', 'hello-world', ['name' => 'World']);
        $this->assertTrue($filesystem->fileExists('hello-world'));
    }

    private function path(string $path): string
    {
        return __DIR__.'/output/'.ltrim($path, '/');
    }

    private function getCanonInstance(Filesystem $outputFilesystem = null): Canon
    {
        return new Canon(
            new Filesystem(new LocalFilesystemAdapter($this->path('/'))),
            new TwigCompiler(new Environment(new ArrayLoader([]))),
            $outputFilesystem
        );
    }
}
