<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector;
use RectorLaravel\ValueObject\ApplyDefaultWithFuncCall;
use PHPStan\Type\ObjectType;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(ApplyDefaultInsteadOfNullCoalesceRector::class, [
        new ApplyDefaultWithFuncCall('config'),
        new \RectorLaravel\ValueObject\ApplyDefaultWithMethodCall(new ObjectType('Illuminate\Http\Request'), 'input'),
        new \RectorLaravel\ValueObject\ApplyDefaultWithStaticCall(new ObjectType('Illuminate\Support\Env'), 'get'),
    ]);
};
