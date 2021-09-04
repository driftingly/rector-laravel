<?php

declare(strict_types=1);

use Rector\Laravel\Rector\ClassMethod\AddArgumentDefaultValueRector;
use Rector\Laravel\ValueObject\AddArgumentDefaultValue;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../../../../../config/config.php');

    $services = $containerConfigurator->services();

    $services->set(AddArgumentDefaultValueRector::class)
        ->call('configure', [[
            AddArgumentDefaultValueRector::ADDED_ARGUMENTS => ValueObjectInliner::inline([
                new AddArgumentDefaultValue('Illuminate\Contracts\Events\Dispatcher', 'listen', 1, null),
            ]),
        ]]);
};
