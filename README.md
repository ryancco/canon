# Generate files from templates

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ryancco/canon.svg?style=flat-square)](https://packagist.org/packages/ryancco/canon)
[![Tests](https://github.com/ryancco/canon/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/ryancco/canon/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/ryancco/canon.svg?style=flat-square)](https://packagist.org/packages/ryancco/canon)

Generate files from templates in PHP. Useful in development for templating commonly created files or files with extensive boilerplate or formatting. Leverage the full power of your preferred templating language by implementing the `Ryancco\Canon\Contracts\Compiler` interface. This package includes an implementation for the [Twig templating engine](https://twig.symfony.com/doc/3.x/templates.html).

Write or read files to and from separate filesystems including local, S/FTP, AWS S3 and more by requiring any of the supported [Flysystem adapters](https://flysystem.thephpleague.com/docs/).

## Installation

You can install the package via composer:

```bash
composer require [--dev] ryancco/canon
```

## Usage

```php
use Ryancco\Canon;
use Ryancco\Canon\Compilers\TwigCompiler;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

// specify a custom twig instance
$canon = new Canon(new Filesystem(new LocalFilesystemAdapter(__DIR__)), new TwigCompiler($twig));

// specify a separate output filesystem
$canon = new Canon(new Filesystem(new LocalFilesystemAdapter(__DIR__)), outputFilesystem: new Filesystem(new LocalFilesystemAdapter(__DIR__.'/../../generated')));

// get started without any customization
$canon = new Canon(new Filesystem(new LocalFilesystemAdapter(__DIR__)));

// using a template file
$canon->generate('hello-world.tpl', 'filename.out', ['name' => 'World']);

// using a template string
$canon->generate('Hello, {{ name }}.', 'filename.out', ['name' => 'World']);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Ryan Colson](https://github.com/ryancco)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
