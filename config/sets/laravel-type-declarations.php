<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector;
use RectorLaravel\Rector\FuncCall\TypeHintTappableCallRector;
use RectorLaravel\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector;
use RectorLaravel\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');
    $rectorConfig->rule(TypeHintTappableCallRector::class);
    $rectorConfig->rule(AddGenericReturnTypeToRelationsRector::class);
    $rectorConfig->rule(EloquentWhereRelationTypeHintingParameterRector::class);
    $rectorConfig->rule(EloquentWhereTypeHintClosureParameterRector::class);
};
