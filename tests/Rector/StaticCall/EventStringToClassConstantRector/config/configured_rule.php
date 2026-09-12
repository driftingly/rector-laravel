<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Transform\ValueObject\StringToClassConstant;
use RectorLaravel\Rector\StaticCall\EventStringToClassConstantRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(EventStringToClassConstantRector::class, [
        new StringToClassConstant('auth.login', 'Illuminate\Auth\Events\Login', 'class'),
        new StringToClassConstant('auth.logout', 'Illuminate\Auth\Events\Logout', 'class'),
    ]);
};
