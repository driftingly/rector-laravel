<?php

declare(strict_types=1);

use Rector\Laravel\Rector\ClassMethod\AddArgumentDefaultValueRector;
use Rector\Laravel\ValueObject\AddArgumentDefaultValue;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../../../../../config/config.php');

    $services = $containerConfigurator->services();

    $services->set(AddArgumentDefaultValueRector::class)
        ->configure([new AddArgumentDefaultValue('Illuminate\Contracts\Events\Dispatcher', 'listen', 1, null)]);
};
