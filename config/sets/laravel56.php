<?php

declare(strict_types=1);

use Rector\Core\ValueObject\Visibility;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector;
use Rector\Visibility\ValueObject\ChangeMethodVisibility;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

# see: https://laravel.com/docs/5.6/upgrade

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(RenameMethodRector::class)
        ->configure([new MethodCallRename(
            'Illuminate\Validation\ValidatesWhenResolvedTrait',
            'validate',
            'validateResolved'
        ),
            new MethodCallRename(
                'Illuminate\Contracts\Validation\ValidatesWhenResolved',
                'validate',
                'validateResolved'
            ),
        ]);

    $services->set(ChangeMethodVisibilityRector::class)
        ->configure(
            [new ChangeMethodVisibility('Illuminate\Routing\Router', 'addRoute', Visibility::PUBLIC),
                new ChangeMethodVisibility('Illuminate\Contracts\Auth\Access\Gate', 'raw', Visibility::PUBLIC),
                new ChangeMethodVisibility('Illuminate\Database\Grammar', 'getDateFormat', Visibility::PUBLIC),
            ]
        );
};
