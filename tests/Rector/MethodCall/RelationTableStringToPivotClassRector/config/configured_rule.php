<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\MethodCall\RelationTableStringToPivotClassRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(RelationTableStringToPivotClassRector::class, [
        RelationTableStringToPivotClassRector::MODEL_NAMESPACES => [
            'RectorLaravel\Tests\Rector\MethodCall\RelationTableStringToPivotClassRector\Source',
        ],
    ]);
};
