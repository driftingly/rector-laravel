<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

// see: https://laravel.com/docs/5.1/upgrade
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');
    $rectorConfig
        ->ruleWithConfiguration(RenameClassRector::class, [
            'Illuminate\Validation\Validator' => 'Illuminate\Contracts\Validation\Validator',
        ]);
};
