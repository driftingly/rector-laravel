<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\ClassMethod\AddGenericBuilderToScopesRector;
use RectorLaravel\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector;
use RectorLaravel\Rector\ClassMethod\AddLegacyGenericReturnTypeToRelationsRector;
use RectorLaravel\Rector\ClassMethod\AddNewGenericReturnTypeToRelationsRector;
use RectorLaravel\Rector\FuncCall\TypeHintTappableCallRector;
use RectorLaravel\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector;
use RectorLaravel\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');
    $rectorConfig->rule(TypeHintTappableCallRector::class);

    // one of the three runs, picked by the installed laravel/framework version via composer constraint
    $rectorConfig->rule(AddLegacyGenericReturnTypeToRelationsRector::class);
    $rectorConfig->rule(AddNewGenericReturnTypeToRelationsRector::class);
    $rectorConfig->rule(AddGenericReturnTypeToRelationsRector::class);

    $rectorConfig->rule(AddGenericBuilderToScopesRector::class);
    $rectorConfig->rule(EloquentWhereRelationTypeHintingParameterRector::class);
    $rectorConfig->rule(EloquentWhereTypeHintClosureParameterRector::class);
};
