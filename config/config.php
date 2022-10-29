<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

use Rector\Core\NonPhpFile\Rector\RenameClassNonPhpRector;

return static function (RectorConfig $rectorConfig): void {
    $services = $rectorConfig->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('RectorLaravel\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/{Rector,ValueObject}']);

    $services->set(RenameClassNonPhpRector::class);
};
