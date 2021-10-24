<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Nette\NodeAnalyzer\BinaryOpAnalyzer;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Rector\Set\ValueObject\LevelSetList;
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

    // reuqired for PHP 8
    $services = $containerConfigurator->services();
    $services->defaults()
        ->public()
        ->autoconfigure()
        ->autowire();

    // needed for sets bellow, only for this split package
    $services->set(BinaryOpAnalyzer::class);
    $services->set(TestsNodeAnalyzer::class);

    $containerConfigurator->import(LevelSetList::UP_TO_PHP_80);
    $containerConfigurator->import(SetList::DEAD_CODE);
};
