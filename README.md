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
