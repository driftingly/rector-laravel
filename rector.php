<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);
    $parameters->set(Option::SKIP, [
        // for tests
        '*/Source/*',
        '*/Fixture/*',
    ]);

    // needed for DEAD_CODE list, just in split package like this
    $containerConfigurator->import(__DIR__ . '/config/config.php');

    $containerConfigurator->import(SetList::PHP_80);
    $containerConfigurator->import(SetList::PHP_74);
    $containerConfigurator->import(SetList::PHP_73);
    $containerConfigurator->import(SetList::DEAD_CODE);
};
