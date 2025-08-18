<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Class_\AddGenericAnnotationToRelationshipMethodsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(AddGenericAnnotationToRelationshipMethodsRector::class);
};
