<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use Rector\Laravel\Set\LaravelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(LaravelSetList::LARAVEL_50);
    $rectorConfig->import(LaravelSetList::LARAVEL_51);
};
