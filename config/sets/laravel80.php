<?php

declare(strict_types=1);

use Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector;
use Rector\Arguments\ValueObject\ArgumentAdder;
use Rector\Laravel\Rector\ClassMethod\AddArgumentDefaultValueRector;
use Rector\Laravel\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector;
use Rector\Laravel\ValueObject\AddArgumentDefaultValue;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\PropertyFetch\RenamePropertyRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Renaming\ValueObject\RenameProperty;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

# see https://laravel.com/docs/8.x/upgrade
return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    # https://github.com/laravel/framework/commit/4d228d6e9dbcbd4d97c45665980d8b8c685b27e6
    $services->set(ArgumentAdderRector::class)
        ->call('configure', [[
            ArgumentAdderRector::ADDED_ARGUMENTS => ValueObjectInliner::inline([
                new ArgumentAdder(
                    'Illuminate\Contracts\Database\Eloquent\Castable',
                    'castUsing',
                    0,
                    'arguments',
                    [], // TODO: Add argument without default value
                    'array'
                ),
            ]),
        ]]);

    # https://github.com/laravel/framework/commit/46084d946cdcd1ae1f32fc87a4f1cc9e3a5bccf6
    $services->set(AddArgumentDefaultValueRector::class)
        ->call('configure', [[
            AddArgumentDefaultValueRector::ADDED_ARGUMENTS => ValueObjectInliner::inline([
                new AddArgumentDefaultValue('Illuminate\Contracts\Events\Dispatcher', 'listen', 1, null),
            ]),
        ]]);

    # https://github.com/laravel/framework/commit/f1289515b27e93248c09f04e3011bb7ce21b2737
    $services->set(AddParentRegisterToEventServiceProviderRector::class);

    $services->set(RenamePropertyRector::class)
        ->call('configure', [[
            RenamePropertyRector::RENAMED_PROPERTIES => ValueObjectInliner::inline([
                # https://github.com/laravel/framework/pull/32092/files
                new RenameProperty('Illuminate\Support\Manager', 'app', 'container'),
                # https://github.com/laravel/framework/commit/4656c2cf012ac62739ab5ea2bce006e1e9fe8f09
                new RenameProperty('Illuminate\Contracts\Queue\ShouldQueue', 'retryAfter', 'backoff'),
                # https://github.com/laravel/framework/commit/12c35e57c0a6da96f36ad77f88f083e96f927205
                new RenameProperty('Illuminate\Contracts\Queue\ShouldQueue', 'timeoutAt', 'retryUntil'),
            ]),
        ]]);

    $services->set(RenameMethodRector::class)
        ->call('configure', [[
            RenameMethodRector::METHOD_CALL_RENAMES => ValueObjectInliner::inline([
                # https://github.com/laravel/framework/pull/32092/files
                new MethodCallRename('Illuminate\Mail\PendingMail', 'sendNow', 'send'),
                # https://github.com/laravel/framework/commit/4656c2cf012ac62739ab5ea2bce006e1e9fe8f09
                new MethodCallRename('Illuminate\Contracts\Queue\ShouldQueue', 'retryAfter', 'backoff'),
                # https://github.com/laravel/framework/commit/12c35e57c0a6da96f36ad77f88f083e96f927205
                new MethodCallRename('Illuminate\Contracts\Queue\ShouldQueue', 'timeoutAt', 'retryUntil'),
                # https://github.com/laravel/framework/commit/f9374fa5fb0450721fb2f90e96adef9d409b112c
                new MethodCallRename('Illuminate\Testing\TestResponse', 'decodeResponseJson', 'json'),
                # https://github.com/laravel/framework/commit/fd662d4699776a94e7ead2a42e82c340363fc5a6
                new MethodCallRename('Illuminate\Testing\TestResponse', 'assertExactJson', 'assertSimilarJson'),
            ]),
        ]]);
};
