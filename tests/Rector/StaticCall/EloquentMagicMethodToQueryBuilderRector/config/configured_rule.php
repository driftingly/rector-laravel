<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');
    $rectorConfig->importNames(importDocBlockNames: false);
    $rectorConfig->importShortClasses(false);
    $rectorConfig->ruleWithConfiguration(
        EloquentMagicMethodToQueryBuilderRector::class,
        [
            EloquentMagicMethodToQueryBuilderRector::EXCLUDE_METHODS => ['excludablePublicMethodBelongsToEloquentQueryBuilder'],
        ],
    );
};
