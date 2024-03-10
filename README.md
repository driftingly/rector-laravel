# Rector Rules for Laravel
[![Tests](https://github.com/driftingly/rector-laravel/actions/workflows/tests.yaml/badge.svg)](https://github.com/driftingly/rector-laravel/actions/workflows/tests.yaml)
[![Code Analysis](https://github.com/driftingly/rector-laravel/actions/workflows/code_analysis.yaml/badge.svg)](https://github.com/driftingly/rector-laravel/actions/workflows/code_analysis.yaml)
[![Packagist Downloads](https://img.shields.io/packagist/dm/driftingly/rector-laravel)](https://packagist.org/packages/driftingly/rector-laravel/stats)
[![Packagist Version](https://img.shields.io/packagist/v/driftingly/rector-laravel)](https://packagist.org/packages/driftingly/rector-laravel)

See available [Laravel rules](/docs/rector_rules_overview.md)

## Install

This package is a [Rector](https://github.com/rectorphp/rector) extension developed by the Laravel community.

Install the `RectorLaravel` package as dependency:

```bash
composer require driftingly/rector-laravel --dev
```

## Use Sets

To add a set to your config, use `RectorLaravel\Set\LaravelSetList` class and pick one of the constants:

```php
<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withSets([
        LaravelSetList::LARAVEL_110
    ]);
```

## Learn Rector Faster

Rector is a tool that [we develop](https://getrector.org/) and share for free, so anyone can save hundreds of hours on refactoring.
But not everyone has time to understand Rector and AST complexity. You have 2 ways to speed this process up:

* Read the book - <a href="https://leanpub.com/rector-the-power-of-automated-refactoring">The Power of Automated Refactoring</a>
* Hire our experienced team to <a href="https://getrector.org/contact">improve your codebase</a>

Both ways support us to and improve Rector in sustainable way by learning from practical projects.
