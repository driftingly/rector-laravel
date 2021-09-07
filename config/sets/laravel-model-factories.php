<?php

declare(strict_types=1);

use Rector\Laravel\Rector\FuncCall\FactoryFuncCallToStaticCallRector;
use Rector\Laravel\Rector\Namespace_\FactoryDefinitionRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(FactoryFuncCallToStaticCallRector::class);
    $services->set(FactoryDefinitionRector::class);
};
