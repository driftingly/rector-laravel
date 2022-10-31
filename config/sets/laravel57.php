<?php

declare(strict_types=1);

use PHPStan\Type\ObjectType;

use Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector;
use Rector\Arguments\ValueObject\ArgumentAdder;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\Visibility;
use Rector\Removing\Rector\ClassMethod\ArgumentRemoverRector;
use Rector\Removing\ValueObject\ArgumentRemover;
use Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector;
use Rector\Visibility\ValueObject\ChangeMethodVisibility;
use RectorLaravel\Rector\Class_\AddMockConsoleOutputFalseToConsoleTestsRector;
use RectorLaravel\Rector\ClassMethod\AddParentBootToModelClassMethodRector;
use RectorLaravel\Rector\MethodCall\ChangeQueryWhereDateValueWithCarbonRector;
use RectorLaravel\Rector\New_\AddGuardToLoginEventRector;
use RectorLaravel\Rector\StaticCall\Redirect301ToPermanentRedirectRector;

# see: https://laravel.com/docs/5.7/upgrade
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');
    $rectorConfig
        ->ruleWithConfiguration(
            ChangeMethodVisibilityRector::class,
            [new ChangeMethodVisibility('Illuminate\Routing\Router', 'addRoute', Visibility::PUBLIC),
                new ChangeMethodVisibility('Illuminate\Contracts\Auth\Access\Gate', 'raw', Visibility::PUBLIC),
            ]
        );
    $rectorConfig
        ->ruleWithConfiguration(
            ArgumentAdderRector::class,
            [new ArgumentAdder('Illuminate\Auth\Middleware\Authenticate', 'authenticate', 0, 'request'),
                new ArgumentAdder(
                    'Illuminate\Foundation\Auth\ResetsPasswords',
                    'sendResetResponse',
                    0,
                    'request',
                    null,
                    new ObjectType('Illuminate\Http\Illuminate\Http')
                ),
                new ArgumentAdder(
                    'Illuminate\Foundation\Auth\SendsPasswordResetEmails',
                    'sendResetLinkResponse',
                    0,
                    'request',
                    null,
                    new ObjectType('Illuminate\Http\Illuminate\Http')
                ),
                new ArgumentAdder('Illuminate\Database\ConnectionInterface', 'select', 2, 'useReadPdo', true),
                new ArgumentAdder('Illuminate\Database\ConnectionInterface', 'selectOne', 2, 'useReadPdo', true),
            ]
        );
    $rectorConfig->rule(Redirect301ToPermanentRedirectRector::class);
    $rectorConfig
        ->ruleWithConfiguration(
            ArgumentRemoverRector::class,
            [new ArgumentRemover('Illuminate\Foundation\Application', 'register', 1, [
                'name' => 'options',
            ]),
            ]
        );
    $rectorConfig->rule(AddParentBootToModelClassMethodRector::class);
    $rectorConfig->rule(ChangeQueryWhereDateValueWithCarbonRector::class);
    $rectorConfig->rule(AddMockConsoleOutputFalseToConsoleTestsRector::class);
    $rectorConfig->rule(AddGuardToLoginEventRector::class);
};
