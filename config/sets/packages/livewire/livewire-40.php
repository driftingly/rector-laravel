<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../config.php');

    // @see https://livewire.laravel.com/docs/4.x/upgrading#update-component-imports
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Livewire\Volt\Component', 'Livewire\Component',
    ]);
};
