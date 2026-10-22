<?php

declare(strict_types=1);
use PHPStan\Type\ObjectType;
use Rector\Config\RectorConfig;
use RectorLaravel\Rector\ArrayDimFetch\ArrayDimFetchToMethodCallRector;
use RectorLaravel\ValueObject\ArrayDimFetchToMethodCall;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    $rectorConfig
        ->ruleWithConfiguration(
            ArrayDimFetchToMethodCallRector::class,
            [
                new ArrayDimFetchToMethodCall(
                    new ObjectType('Illuminate\Contracts\Config\Repository'),
                    'get',
                    'set',
                    'has',
                    'set', // intentional
                ),
                new ArrayDimFetchToMethodCall(
                    new ObjectType('Illuminate\Contracts\Cache\Repository'),
                    'get',
                    'set',
                    'has',
                    'forget',
                ),
                new ArrayDimFetchToMethodCall(
                    new ObjectType('Illuminate\Contracts\Container\Container'),
                    'make',
                    'bind',
                    'bound',
                ),
            ],
        );
};
