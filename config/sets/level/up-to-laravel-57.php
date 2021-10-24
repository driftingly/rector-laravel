<?php

declare(strict_types=1);

use Rector\Laravel\Set\LaravelLevelSetList;
use Rector\Laravel\Set\LaravelSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(LaravelSetList::LARAVEL_57);
    $containerConfigurator->import(LaravelLevelSetList::UP_TO_LARAVEL_56);
};