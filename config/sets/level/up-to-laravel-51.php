<?php

declare(strict_types=1);

use Rector\Laravel\Set\LaravelSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(LaravelSetList::LARAVEL_50);
    $containerConfigurator->import(LaravelSetList::LARAVEL_51);
};