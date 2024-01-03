<?php

declare(strict_types=1);

use PHPStan\Type\ArrayType;
use PHPStan\Type\MixedType;
use Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector;
use Rector\Arguments\ValueObject\ArgumentAdder;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\PropertyFetch\RenamePropertyRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Renaming\ValueObject\RenameProperty;
use RectorLaravel\Rector\ClassMethod\AddArgumentDefaultValueRector;
use RectorLaravel\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector;
use RectorLaravel\ValueObject\AddArgumentDefaultValue;

// see https://laravel.com/docs/8.x/upgrade
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    // https://github.com/laravel/framework/commit/4d228d6e9dbcbd4d97c45665980d8b8c685b27e6
    $rectorConfig
        ->ruleWithConfiguration(ArgumentAdderRector::class, [new ArgumentAdder(
            'Illuminate\Contracts\Database\Eloquent\Castable',
            'castUsing',
            0,
            'arguments',
            [], // TODO: Add argument without default value
            new ArrayType(new MixedType, new MixedType)
        ),
        ]);

    // https://github.com/laravel/framework/commit/46084d946cdcd1ae1f32fc87a4f1cc9e3a5bccf6
    $rectorConfig
        ->ruleWithConfiguration(
            AddArgumentDefaultValueRector::class,
            [new AddArgumentDefaultValue('Illuminate\Contracts\Events\Dispatcher', 'listen', 1, null)]
        );

    // https://github.com/laravel/framework/commit/f1289515b27e93248c09f04e3011bb7ce21b2737
    $rectorConfig->rule(AddParentRegisterToEventServiceProviderRector::class);

    $rectorConfig
        ->ruleWithConfiguration(RenamePropertyRector::class, [                // https://github.com/laravel/framework/pull/32092/files
            new RenameProperty('Illuminate\Support\Manager', 'app', 'container'),
            // https://github.com/laravel/framework/commit/4656c2cf012ac62739ab5ea2bce006e1e9fe8f09
            new RenameProperty('Illuminate\Contracts\Queue\ShouldQueue', 'retryAfter', 'backoff'),
            // https://github.com/laravel/framework/commit/12c35e57c0a6da96f36ad77f88f083e96f927205
            new RenameProperty('Illuminate\Contracts\Queue\ShouldQueue', 'timeoutAt', 'retryUntil'),
        ]);

    $rectorConfig
        ->ruleWithConfiguration(RenameMethodRector::class, [                // https://github.com/laravel/framework/pull/32092/files
            new MethodCallRename('Illuminate\Mail\PendingMail', 'sendNow', 'send'),
            // https://github.com/laravel/framework/commit/4656c2cf012ac62739ab5ea2bce006e1e9fe8f09
            new MethodCallRename('Illuminate\Contracts\Queue\ShouldQueue', 'retryAfter', 'backoff'),
            // https://github.com/laravel/framework/commit/12c35e57c0a6da96f36ad77f88f083e96f927205
            new MethodCallRename('Illuminate\Contracts\Queue\ShouldQueue', 'timeoutAt', 'retryUntil'),
            // https://github.com/laravel/framework/commit/f9374fa5fb0450721fb2f90e96adef9d409b112c
            new MethodCallRename('Illuminate\Testing\TestResponse', 'decodeResponseJson', 'json'),
        ]);
};
