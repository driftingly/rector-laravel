<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Removing\Rector\Class_\RemoveTraitUseRector;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');
    $rectorConfig
        ->ruleWithConfiguration(RemoveTraitUseRector::class, [
            // see https://laravel.com/docs/5.3/upgrade
            'Illuminate\Foundation\Auth\Access\AuthorizesResources',
        ]);

    $rectorConfig
        ->ruleWithConfiguration(RenameMethodRector::class, [
            // https://laravel.com/docs/5.3/upgrade#5.2-deprecations
            new MethodCallRename('Illuminate\Support\Collection', 'lists', 'pluck'),
            new MethodCallRename('Illuminate\Database\Query\Builder', 'lists', 'pluck'),
        ]);
};
