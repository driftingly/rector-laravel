<?php

declare(strict_types=1);
use PHPStan\Type\ObjectType;
use Rector\Config\RectorConfig;
use Rector\Transform\Rector\ArrayDimFetch\ArrayDimFetchToMethodCallRector;
use Rector\Transform\ValueObject\ArrayDimFetchToMethodCall;

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
