<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Namespace_\FactoryDefinitionRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(FactoryDefinitionRector::class, [
        'RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Fixture\Configured\Model',
        'App\User',
        'RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Fixture\Configured\Account',
        'RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector\Source\Model',
    ]);
};
