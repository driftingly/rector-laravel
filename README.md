# Rector Rules for Laravel

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
use RectorLaravel\Set\LaravelSetList;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        LaravelSetList::LARAVEL_90
    ]);
};
```

<br>

## Learn Rector Faster

Rector is a tool that [we develop](https://getrector.org/) and share for free, so anyone can save hundreds of hours on refactoring. But not everyone has time to understand Rector and AST complexity. You have 2 ways to speed this process up:

* read a book - <a href="https://leanpub.com/rector-the-power-of-automated-refactoring">The Power of Automated Refactoring</a>
* hire our experienced team to <a href="https://getrector.org/contact">improve your codebase</a>

Both ways support us to and improve Rector in sustainable way by learning from practical projects.
