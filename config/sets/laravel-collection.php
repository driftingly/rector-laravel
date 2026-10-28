<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\BooleanNot\AvoidNegatedCollectionContainsOrDoesntContainRector;
use RectorLaravel\Rector\Expr\CollectionCountComparisonToIsEmptyRector;
use RectorLaravel\Rector\MethodCall\AvoidNegatedCollectionFilterOrRejectRector;
use RectorLaravel\Rector\MethodCall\CollectionWhereToContainsRector;
use RectorLaravel\Rector\MethodCall\ConvertEnumerableToArrayToAllRector;
use RectorLaravel\Rector\MethodCall\UnaliasCollectionMethodsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');
    $rectorConfig->rule(AvoidNegatedCollectionContainsOrDoesntContainRector::class);
    $rectorConfig->rule(AvoidNegatedCollectionFilterOrRejectRector::class);
    $rectorConfig->rule(CollectionCountComparisonToIsEmptyRector::class);
    $rectorConfig->rule(CollectionWhereToContainsRector::class);
    $rectorConfig->rule(ConvertEnumerableToArrayToAllRector::class);
    $rectorConfig->rule(UnaliasCollectionMethodsRector::class);
};
