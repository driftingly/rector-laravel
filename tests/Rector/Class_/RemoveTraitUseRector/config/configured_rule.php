<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Class_\RemoveTraitUseRector;
use RectorLaravel\Tests\Rector\Class_\RemoveTraitUseRector\Source\SomeTrait;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');
    $rectorConfig->ruleWithConfiguration(RemoveTraitUseRector::class, [SomeTrait::class]);
};
