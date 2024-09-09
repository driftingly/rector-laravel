<?php

declare(strict_types=1);
use PHPStan\Type\ObjectType;
use Rector\Config\RectorConfig;
use Rector\Transform\Rector\ArrayDimFetch\ArrayDimFetchToMethodCallRector;
use Rector\Transform\ValueObject\ArrayDimFetchToMethodCall;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    $rectorConfig->ruleWithConfiguration(ArrayDimFetchToMethodCallRector::class, [
        new ArrayDimFetchToMethodCall(new ObjectType('Illuminate\Foundation\Application'), 'make'),
        new ArrayDimFetchToMethodCall(new ObjectType('Illuminate\Contracts\Foundation\Application'), 'make'),
        new ArrayDimFetchToMethodCall(new ObjectType('Illuminate\Config\Repository'), 'get'),
        new ArrayDimFetchToMethodCall(new ObjectType('Illuminate\Contracts\Config\Repository'), 'make'),
        new ArrayDimFetchToMethodCall(new ObjectType('Illuminate\Contracts\Container\Container\Application'), 'make'),
        new ArrayDimFetchToMethodCall(new ObjectType('Illuminate\Contracts\Container\Container'), 'make'),
    ]);
};
