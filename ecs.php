<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/config',
        __DIR__ . '/ecs.php',
    ]);

    $parameters->set(Option::SETS, [SetList::PSR_12, SetList::SYMPLIFY, SetList::COMMON, SetList::CLEAN_CODE]);
    $parameters->set(Option::SKIP, [
        '*/Source/*', '*/Fixture/*',
        // breaks annotated code - removed on symplify dev-main
        \PhpCsFixer\Fixer\ReturnNotation\ReturnAssignmentFixer::class,
    ]);
    $parameters->set(Option::LINE_ENDING, "\n");
};
