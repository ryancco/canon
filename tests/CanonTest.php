<?php

declare(strict_types=1);

namespace Ryancco\Canon\Tests;

use Illuminate\Filesystem\Filesystem as IlluminateFilesystem;
use Illuminate\View\Compilers\BladeCompiler as Blade;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Ryancco\Canon\Canon;
use Ryancco\Canon\Compilers\BladeCompiler;
use Ryancco\Canon\Compilers\NativeCompiler;
use Ryancco\Canon\Compilers\TwigCompiler;
use Ryancco\Canon\Contracts\Compiler;
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

    /** @dataProvider providesTemplates */
    public function test_it_generates_a_file_from_a_template_file(Compiler $compiler, string $template, array $data): void
    {
        file_put_contents($this->path('hello-world'), $template);
        $this->getCanonInstance($compiler)->generate('hello-world', 'hello-world.out', $data);
        $this->assertFileExists($this->path('hello-world.out'));
    }

    /** @dataProvider providesTemplates */
    public function test_it_generates_a_file_from_a_template_string(Compiler $compiler, string $template, array $data): void
    {
        $this->getCanonInstance($compiler)->generate($template, 'hello-world.out', $data);
        $this->assertFileExists($this->path('hello-world.out'));
    }

    /** @dataProvider providesTemplates */
    public function test_it_overwrites_a_file(Compiler $compiler, string $template, array $data, string $expectedOutput): void
    {
        @unlink($this->path('existing-file'));
        touch($this->path('existing-file'));
        $this->getCanonInstance($compiler)->generate($template, 'existing-file', $data);
        $this->assertEquals($expectedOutput, file_get_contents($this->path('existing-file')));
    }

    /** @dataProvider providesTemplates */
    public function test_it_replaces_variables(Compiler $compiler, string $template, array $data, string $expectedOutput): void
    {
        $this->getCanonInstance($compiler)->generate($template, 'hello-world.out', $data);
        $this->assertEquals($expectedOutput, file_get_contents($this->path('hello-world.out')));
    }

    /** @dataProvider providesTemplates */
    public function test_it_generates_a_file_to_a_separate_output_filesystem(Compiler $compiler, string $template, array $data, string $expectedOutput): void
    {
        $this->getCanonInstance($compiler, $filesystem = new Filesystem(new InMemoryFilesystemAdapter()))->generate($template, 'hello-world.out', $data);
        $this->assertTrue($filesystem->fileExists('hello-world.out'));
    }

    private function path(string $path): string
    {
        return __DIR__.'/output/'.ltrim($path, '/');
    }

    private function getCanonInstance(Compiler $compiler, Filesystem $outputFilesystem = null): Canon
    {
        return new Canon(
            new Filesystem(new LocalFilesystemAdapter($this->path('/'))),
            $compiler,
            $outputFilesystem
        );
    }

    public function providesTemplates(): array
    {
        return [
            NativeCompiler::class => [
                new NativeCompiler(),
                'Hello, { name }.',
                ['name' => 'World'],
                'Hello, World.',
            ],
            TwigCompiler::class => [
                new TwigCompiler(new Environment(new ArrayLoader())),
                'Hello, {{ name }}.',
                ['name' => 'World'],
                'Hello, World.',
            ],
            BladeCompiler::class => [
                new BladeCompiler(new Blade(new IlluminateFilesystem(), $this->path(''))),
                'Hello, {{ $name }}.',
                ['name' => 'World'],
                'Hello, World.',
            ],
        ];
    }
}
