<?php

declare(strict_types=1);

use PHPStan\Type\ObjectType;

use Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector;
use Rector\Arguments\ValueObject\ArgumentAdder;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\Visibility;
use Rector\Laravel\Rector\Class_\AddMockConsoleOutputFalseToConsoleTestsRector;
use Rector\Laravel\Rector\ClassMethod\AddParentBootToModelClassMethodRector;
use Rector\Laravel\Rector\MethodCall\ChangeQueryWhereDateValueWithCarbonRector;
use Rector\Laravel\Rector\New_\AddGuardToLoginEventRector;
use Rector\Laravel\Rector\StaticCall\Redirect301ToPermanentRedirectRector;
use Rector\Removing\Rector\ClassMethod\ArgumentRemoverRector;
use Rector\Removing\ValueObject\ArgumentRemover;
use Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector;
use Rector\Visibility\ValueObject\ChangeMethodVisibility;

# see: https://laravel.com/docs/5.7/upgrade
return static function (RectorConfig $rectorConfig): void {
    $services = $rectorConfig->services();
    $services->set(ChangeMethodVisibilityRector::class)
        ->configure(
            [new ChangeMethodVisibility('Illuminate\Routing\Router', 'addRoute', Visibility::PUBLIC),
                new ChangeMethodVisibility('Illuminate\Contracts\Auth\Access\Gate', 'raw', Visibility::PUBLIC),
            ]
        );
    $services->set(ArgumentAdderRector::class)
        ->configure(
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
    $services->set(Redirect301ToPermanentRedirectRector::class);
    $services->set(ArgumentRemoverRector::class)
        ->configure([new ArgumentRemover('Illuminate\Foundation\Application', 'register', 1, [
            'name' => 'options',
        ]),
        ]);
    $services->set(AddParentBootToModelClassMethodRector::class);
    $services->set(ChangeQueryWhereDateValueWithCarbonRector::class);
    $services->set(AddMockConsoleOutputFalseToConsoleTestsRector::class);
    $services->set(AddGuardToLoginEventRector::class);
};
