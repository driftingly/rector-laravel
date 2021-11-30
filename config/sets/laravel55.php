<?php

declare(strict_types=1);

use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\Rector\PropertyFetch\RenamePropertyRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Renaming\ValueObject\RenameProperty;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

# see: https://laravel.com/docs/5.5/upgrade

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(RenameMethodRector::class)
        ->configure([new MethodCallRename('Illuminate\Console\Command', 'fire', 'handle')]);

    $services->set(RenamePropertyRector::class)
        ->configure(
            [new RenameProperty('Illuminate\Database\Eloquent\Concerns\HasEvents', 'events', 'dispatchesEvents'),
                new RenameProperty('Illuminate\Database\Eloquent\Relations\Pivot', 'parent', 'pivotParent'),
            ]
        );

    $services->set(RenameClassRector::class)
        ->configure([
            'Illuminate\Translation\LoaderInterface' => 'Illuminate\Contracts\Translation\Loader',
        ]);
};
