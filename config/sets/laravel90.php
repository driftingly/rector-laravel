<?php

declare(strict_types=1);

use PHPStan\Type\ArrayType;

use PHPStan\Type\MixedType;
use Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector;
use Rector\Arguments\ValueObject\ArgumentAdder;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\Visibility;
use Rector\Laravel\Rector\ClassMethod\AddArgumentDefaultValueRector;
use Rector\Laravel\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector;
use Rector\Laravel\Rector\MethodCall\RemoveAllOnDispatchingMethodsWithJobChainingRector;
use Rector\Laravel\ValueObject\AddArgumentDefaultValue;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\PropertyFetch\RenamePropertyRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Renaming\ValueObject\RenameProperty;
use Rector\Visibility\ValueObject\ChangeMethodVisibility;

# see https://laravel.com/docs/9.x/upgrade
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig
        ->ruleWithConfiguration(ArgumentAdderRector::class, [new ArgumentAdder(
            'Illuminate\Contracts\Foundation\Application',
            'storagePath',
            0,
            'path',
            ''
        ),
    ]);

    $rectorConfig
        ->ruleWithConfiguration(ArgumentAdderRector::class, [new ArgumentAdder(
            'Illuminate\Contracts\Foundation\Application',
            'langPath',
            0,
            'path',
            ''
        ),
    ]);

    $rectorConfig
        ->ruleWithConfiguration(ChangeMethodVisibilityRector::class, [new ChangeMethodVisibility(
            'Illuminate\Contracts\Foundation\Application',
            'ignore',
            Visibility::PUBLIC
        ),
    ]);


    $rectorConfig
        ->ruleWithConfiguration(RenameMethodRector::class, [
            new MethodCallRename('Illuminate\Support\Enumerable', 'reduceWithKeys', 'reduce'),

            new MethodCallRename('Illuminate\Support\Enumerable', 'reduceMany', 'reduceSpread'),
        ]);
};
