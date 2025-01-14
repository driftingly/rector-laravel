<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\BooleanNot\AvoidNegatedCollectionContainsOrDoesntContainRector;
use RectorLaravel\Rector\MethodCall\AvoidNegatedCollectionFilterOrRejectRector;
use RectorLaravel\Rector\MethodCall\UnaliasCollectionMethodsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');
    $rectorConfig->rule(AvoidNegatedCollectionContainsOrDoesntContainRector::class);
    $rectorConfig->rule(AvoidNegatedCollectionFilterOrRejectRector::class);
    $rectorConfig->rule(UnaliasCollectionMethodsRector::class);
};
