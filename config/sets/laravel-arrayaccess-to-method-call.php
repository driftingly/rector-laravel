<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    $rectorConfig
        ->ruleWithConfiguration(
            \Rector\Transform\Rector\ArrayDimFetch\ArrayDimFetchToMethodCallRector::class,
            [
                new \Rector\Transform\ValueObject\ArrayDimFetchToMethodCall(
                    new \PHPStan\Type\ObjectType('Illuminate\Foundation\Application'),
                    'make',
                ),
                new \Rector\Transform\ValueObject\ArrayDimFetchToMethodCall(
                    new \PHPStan\Type\ObjectType('Illuminate\Config\Repository'),
                    'get',
                ),
            ],
        );
};
