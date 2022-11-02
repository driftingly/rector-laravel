<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use Rector\Removing\Rector\Class_\RemoveTraitUseRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');
    $rectorConfig
        ->ruleWithConfiguration(RemoveTraitUseRector::class, [
            # see https://laravel.com/docs/5.3/upgrade
            'Illuminate\Foundation\Auth\Access\AuthorizesResources',
        ]);
};
