<?php

declare(strict_types=1);

use PHPStan\Type\ObjectType;
use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector;
use RectorLaravel\ValueObject\ApplyDefaultWithFuncCall;
use RectorLaravel\ValueObject\ApplyDefaultWithMethodCall;
use RectorLaravel\ValueObject\ApplyDefaultWithStaticCall;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(ApplyDefaultInsteadOfNullCoalesceRector::class, [
        new ApplyDefaultWithFuncCall('config'),
        new ApplyDefaultWithMethodCall(new ObjectType('Illuminate\Http\Request'), 'input'),
        new ApplyDefaultWithStaticCall(new ObjectType('Illuminate\Support\Env'), 'get'),
    ]);
};
