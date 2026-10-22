<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersionFeature;
use RectorLaravel\Rector\StaticCall\StaticCallToMethodCallRector;
use RectorLaravel\ValueObject\StaticCallToMethodCall;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->phpVersion(PhpVersionFeature::TYPED_PROPERTIES);

    $rectorConfig
        ->ruleWithConfiguration(StaticCallToMethodCallRector::class, [
            new StaticCallToMethodCall(
                'Illuminate\Support\Facades\App',
                '*',
                'Illuminate\Foundation\Application',
                '*'
            ),
        ]);
};
