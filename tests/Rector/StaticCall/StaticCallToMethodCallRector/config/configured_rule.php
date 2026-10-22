<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersionFeature;
use RectorLaravel\Rector\StaticCall\StaticCallToMethodCallRector;
use RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source\TargetFileSystem;
use RectorLaravel\ValueObject\StaticCallToMethodCall;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->phpVersion(PhpVersionFeature::PROPERTY_PROMOTION);

    $rectorConfig
        ->ruleWithConfiguration(StaticCallToMethodCallRector::class, [
            new StaticCallToMethodCall('Nette\Utils\FileSystem', 'write', TargetFileSystem::class, 'dumpFile'),
            new StaticCallToMethodCall(
                'Illuminate\Support\Facades\Response',
                '*',
                'Illuminate\Contracts\Routing\ResponseFactory',
                '*'
            ),
            new StaticCallToMethodCall(
                'Illuminate\Support\Facades\App',
                '*',
                'Illuminate\Foundation\Application',
                '*'
            ),
        ]);
};
