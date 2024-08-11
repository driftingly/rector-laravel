# Rector Rules for Laravel

[![Tests](https://github.com/driftingly/rector-laravel/actions/workflows/tests.yaml/badge.svg)](https://github.com/driftingly/rector-laravel/actions/workflows/tests.yaml)
[![Code Analysis](https://github.com/driftingly/rector-laravel/actions/workflows/code_analysis.yaml/badge.svg)](https://github.com/driftingly/rector-laravel/actions/workflows/code_analysis.yaml)
[![Packagist Downloads](https://img.shields.io/packagist/dm/driftingly/rector-laravel)](https://packagist.org/packages/driftingly/rector-laravel/stats)
[![Packagist Version](https://img.shields.io/packagist/v/driftingly/rector-laravel)](https://packagist.org/packages/driftingly/rector-laravel)

See available [Laravel rules](/docs/rector_rules_overview.md)

## Install

This package is a [Rector](https://github.com/rectorphp/rector) extension developed by the Laravel community.

Rules for additional first party packages are included as well e.g. Cashier and Livewire.

Install as a dev dependency:

```bash
composer require --dev driftingly/rector-laravel
```

## Automate Laravel Upgrades

To add a set to your config, use `RectorLaravel\Set\LaravelLevelSetList` and pick the constant that matches your target version.
Sets for higher versions include sets for lower versions.

```php
<?php declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelLevelSetList;

return RectorConfig::configure()
    ->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_110,
    ]);
```

The sets in `RectorLaravel\Set\LaravelSetList` only contain changes related to a specific version upgrade.
For example, the rules in `LaravelSetList::LARAVEL_110` apply when upgrading from Laravel 10 to Laravel 11.

## Additional Sets

To improve different aspects of your code, use the sets in `RectorLaravel\Set\LaravelSetList`.

```php
<?php declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withSets([
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
        ...
    ]);
```

| Set                                                                                                                                                                                         | Purpose                                                                                                                                                                                                                                          |
|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [LaravelSetList::LARAVEL_ARRAYACCESS_TO_METHOD_CALL](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-arrayaccess-to-method-call.php)                             | Converts uses of things like `$app['config']` to `$app->make('config')`.                                                                                                                                                                         |
| [LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-array-str-functions-to-static-call.php)              | Converts most string and array helpers into Str and Arr Facades' static calls.<br/>https://laravel.com/docs/11.x/facades#facades-vs-helper-functions                                                                                             |
| [LaravelSetList::LARAVEL_CODE_QUALITY](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-code-quality.php)                                                         | Replaces magical call on `$this->app["something"]` to standalone variable with PHPDocs.                                                                                                                                                          |
| [LaravelSetList::LARAVEL_COLLECTION](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-collection.php)                                                             | Improves the usage of Laravel Collections by using simpler, more efficient, or more readable methods.                                                                                                                                            |
| [LaravelSetList::LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-container-string-to-fully-qualified-name.php) | Changes the string or class const used for a service container make call.<br/>https://laravel.com/docs/11.x/container#the-make-method                                                                                                            |
| [LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-eloquent-magic-method-to-query-builder.php)     | Transforms magic method calls on Eloquent Models into corresponding Query Builder method calls.<br/>https://laravel.com/docs/11.x/eloquent                                                                                                       |
| [LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-facade-aliases-to-full-names.php)                         | Replaces Facade aliases with full Facade names.<br/>https://laravel.com/docs/11.x/facades#facade-class-reference                                                                                                                                 |
| [LaravelSetList::LARAVEL_IF_HELPERS](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-if-helpers.php)                                                             | Replaces `abort()`, `report()`, `throw` statements inside conditions with `abort_if()`, `report_if()`, `throw_if()` function calls.<br/>https://laravel.com/docs/11.x/helpers#method-abort-if                                                    |
| [LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-legacy-factories-to-classes.php)                           | Migrates Eloquent legacy model factories (with closures) into class based factories.<br/>https://laravel.com/docs/8.x/releases#model-factory-classes                                                                                             |
| [LaravelSetList::LARAVEL_STATIC_TO_INJECTION](https://github.com/driftingly/rector-laravel/blob/main/config/sets/laravel-static-to-injection.php)                                           | Replaces Laravel's Facades with Dependency Injection.<br/>https://tomasvotruba.com/blog/2019/03/04/how-to-turn-laravel-from-static-to-dependency-injection-in-one-day/<br/>https://laravel.com/docs/11.x/facades#facades-vs-dependency-injection |

## Contributors

Thank you everyone who works so hard on improving this package:

- [@TomasVotruba](https://github.com/TomasVotruba)
- [@peterfox](https://github.com/peterfox)
- [@GeniJaho](https://github.com/GeniJaho)
- [@driftingly](https://github.com/driftingly)
- [All Contributors](https://github.com/driftingly/rector-laravel/graphs/contributors)

## Hire The Rector Team

Rector is a tool that [we develop](https://getrector.com/) and share for free, so anyone can automate their refactoring. But not everyone has dozens of hours to understand complexity of abstract-syntax-tree in their own time. **That's why we provide commercial support - to save your time**.

Would you like to apply Rector on your code base but don't have time for the struggle with your project? [Hire the Rector team](https://getrector.com/contact) to get there faster.

## Learn Rector Faster

Not everyone has time to understand Rector and AST complexity. You can speed up the process by reading the book [The Power of Automated Refactoring](https://leanpub.com/rector-the-power-of-automated-refactoring). Not only will it help you learn and understand Rector but it supports the project as well.
