<?php

declare(strict_types=1);

use Rector\Removing\Rector\Class_\RemoveTraitUseRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(RemoveTraitUseRector::class)
        ->configure([
            # see https://laravel.com/docs/5.3/upgrade
            'Illuminate\Foundation\Auth\Access\AuthorizesResources',
        ]);
};
