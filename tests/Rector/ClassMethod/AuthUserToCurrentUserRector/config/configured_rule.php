<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\ClassMethod\AuthUserToCurrentUserRector;
use RectorLaravel\Tests\Rector\ClassMethod\AuthUserToCurrentUserRector\Source\User;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(AuthUserToCurrentUserRector::class, [
        'userModel' => User::class,
    ]);
};
