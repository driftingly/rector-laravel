<?php

declare(strict_types=1);

use Rector\Core\NonPhpFile\Rector\RenameClassNonPhpRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Rector\\Laravel\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/{Rector,ValueObject}']);

    $services->set(RenameClassNonPhpRector::class);
};
