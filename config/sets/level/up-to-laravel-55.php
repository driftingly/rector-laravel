<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([LaravelSetList::LARAVEL_55, LaravelLevelSetList::UP_TO_LARAVEL_54]);
};
