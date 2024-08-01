<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Class_\RenameAttributeRector;
use Rector\Renaming\ValueObject\RenameAttribute;
use RectorLaravel\Rector\Class_\LivewireComponentComputedMethodToComputedAttributeRector;
use RectorLaravel\Rector\Class_\LivewireComponentQueryStringToUrlAttributeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../config.php');

    $rectorConfig->rule(LivewireComponentQueryStringToUrlAttributeRector::class);
    $rectorConfig->rule(LivewireComponentComputedMethodToComputedAttributeRector::class);

    $rectorConfig->ruleWithConfiguration(RenameAttributeRector::class, [
        new RenameAttribute('Livewire\Attributes\Rule', 'Livewire\Attributes\Validate'),
    ]);
};
