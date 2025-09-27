<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(AddUseAnnotationToHasFactoryTraitRector::class);
};
