<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Removing\Rector\Class_\RemoveInterfacesRector;
use Rector\Removing\Rector\Class_\RemoveTraitUseRector;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Transform\Rector\StaticCall\StaticCallToFuncCallRector;
use Rector\Transform\ValueObject\StaticCallToFuncCall;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    $rectorConfig
        ->ruleWithConfiguration(RemoveTraitUseRector::class, [
            // see https://laravel.com/docs/5.3/upgrade
            'Illuminate\Foundation\Auth\Access\AuthorizesResources',
        ]);

    // https://laravel.com/docs/5.3/upgrade#5.2-deprecations
    $rectorConfig
        ->ruleWithConfiguration(RenameMethodRector::class, [
            new MethodCallRename('Illuminate\Support\Collection', 'lists', 'pluck'),
            new MethodCallRename('Illuminate\Database\Query\Builder', 'lists', 'pluck'),
            new MethodCallRename('Illuminate\Database\Eloquent\Collection', 'withHidden', 'makeVisible'),
            new MethodCallRename('Illuminate\Database\Eloquent\Model', 'withHidden', 'makeVisible'),
        ]);

    $rectorConfig
        ->ruleWithConfiguration(RemoveInterfacesRector::class, [
            'Illuminate\Contracts\Bus\SelfHandling',
        ]);

    $rectorConfig
        ->ruleWithConfiguration(RenameClassRector::class, [
            'Illuminate\Database\Eloquent\ScopeInterface' => 'Illuminate\Database\Eloquent\Scope',
            'Illuminate\View\Expression' => 'Illuminate\Support\HtmlString',
        ]);

    $rectorConfig
        ->ruleWithConfiguration(StaticCallToFuncCallRector::class, [
            new StaticCallToFuncCall('Illuminate\Support\Str', 'randomBytes', 'random_bytes'),
            new StaticCallToFuncCall('Illuminate\Support\Str', 'equals', 'hash_equals'),
        ]);
};
