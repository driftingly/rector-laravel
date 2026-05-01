<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

// see https://laravel.com/docs/13.x/upgrade
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    // see https://laravel.com/docs/13.x/upgrade#request-forgery-protection
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Illuminate\Foundation\Http\Middleware\VerifyCsrfToken' => 'Illuminate\Foundation\Http\Middleware\PreventRequestForgery',
        'Illuminate\Foundation\Http\Middleware\ValidateCsrfToken' => 'Illuminate\Foundation\Http\Middleware\PreventRequestForgery',
    ]);
};
