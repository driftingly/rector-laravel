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
## Available Sets

| Set                       | Purpose                                                                                                                                                                                                                                          |
|---------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-array-str-functions-to-static-call.php) | <br/> Converts most string and array helpers into Str and Arr Facades' static calls. https://laravel.com/docs/11.x/facades#facades-vs-helper-functions                                                                                           |
| [LaravelSetList::LARAVEL_CODE_QUALITY](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-code-quality.php) | Replaces magical call on `$this->app["something"]` to standalone type assign variable.                                                                                                                                                           |
| [LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-eloquent-magic-method-to-query-builder.php) | Transforms certain magic method calls on Eloquent Models into corresponding Query Builder method calls.<br/>https://laravel.com/docs/11.x/eloquent                                                                                               |
| [LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-facade-aliases-to-full-names.php) | Replaces Facade aliases with full Facade names.<br/>https://laravel.com/docs/11.x/facades#facade-class-reference                                                                                                                                 |
| [LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-legacy-factories-to-classes.php) | Migrates Eloquent legacy model factories (with closures) into class based factories.<br/>https://laravel.com/docs/8.x/releases#model-factory-classes                                                                                             |
| [LaravelSetList::LARAVEL_STATIC_TO_INJECTION](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-static-to-injection.php) | Replaces Laravel's Facades with Dependency Injection.<br/>https://tomasvotruba.com/blog/2019/03/04/how-to-turn-laravel-from-static-to-dependency-injection-in-one-day/<br/>https://laravel.com/docs/11.x/facades#facades-vs-dependency-injection |


## Learn Rector Faster

Rector is a tool that [we develop](https://getrector.org/) and share for free, so anyone can save hundreds of hours on refactoring.
But not everyone has time to understand Rector and AST complexity. You have 2 ways to speed this process up:

* Read the book - <a href="https://leanpub.com/rector-the-power-of-automated-refactoring">The Power of Automated Refactoring</a>
* Hire our experienced team to <a href="https://getrector.org/contact">improve your codebase</a>

Both ways support us to and improve Rector in sustainable way by learning from practical projects.
