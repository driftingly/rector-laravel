<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use Rector\Laravel\Set\LaravelLevelSetList;
use Rector\Laravel\Set\LaravelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([LaravelSetList::LARAVEL_90, LaravelLevelSetList::UP_TO_LARAVEL_80]);
};
