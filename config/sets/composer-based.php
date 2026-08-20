<?php

declare(strict_types=1);

use Rector\Composer\InstalledPackageResolver;
use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelSetList;
use RectorLaravel\Set\Packages\Faker\FakerSetList;
use RectorLaravel\Set\Packages\Livewire\LivewireSetList;

/**
 * Sets bound to the package versions installed in the analysed project, picked up by
 * `RectorConfig::configure()->withComposerBased(laravel: true)`.
 *
 * A version set is imported once the installed package reaches that version and stays active from there upwards,
 * so a direct upgrade over several major versions is covered by a single composer-based set.
 */
return static function (RectorConfig $rectorConfig): void {
    /** @var array<string, array<string, string>> $versionSetsByPackage package name => minimal version => set file */
    $versionSetsByPackage = [
        'laravel/framework' => [
            '5.0' => LaravelSetList::LARAVEL_50,
            '5.1' => LaravelSetList::LARAVEL_51,
            '5.2' => LaravelSetList::LARAVEL_52,
            '5.3' => LaravelSetList::LARAVEL_53,
            '5.4' => LaravelSetList::LARAVEL_54,
            '5.5' => LaravelSetList::LARAVEL_55,
            '5.6' => LaravelSetList::LARAVEL_56,
            '5.7' => LaravelSetList::LARAVEL_57,
            '5.8' => LaravelSetList::LARAVEL_58,
            '6.0' => LaravelSetList::LARAVEL_60,
            '7.0' => LaravelSetList::LARAVEL_70,
            '8.0' => LaravelSetList::LARAVEL_80,
            '9.0' => LaravelSetList::LARAVEL_90,
            '10.0' => LaravelSetList::LARAVEL_100,
            '11.0' => LaravelSetList::LARAVEL_110,
            '12.0' => LaravelSetList::LARAVEL_120,
            '13.0' => LaravelSetList::LARAVEL_130,
        ],
        'fakerphp/faker' => [
            '1.0' => FakerSetList::FAKER_10,
        ],
        'livewire/livewire' => [
            '3.0' => LivewireSetList::LIVEWIRE_30,
            '4.0' => LivewireSetList::LIVEWIRE_40,
        ],
    ];

    $installedPackageResolver = $rectorConfig->make(InstalledPackageResolver::class);

    foreach ($versionSetsByPackage as $packageName => $versionSets) {
        $installedVersion = $installedPackageResolver->resolvePackageVersion($packageName);

        // not installed, or a non-comparable dev version
        if ($installedVersion === null || preg_match('/^\d/', $installedVersion) !== 1) {
            continue;
        }

        foreach ($versionSets as $minimalVersion => $setFilePath) {
            if (version_compare($installedVersion, $minimalVersion, '>=')) {
                $rectorConfig->import($setFilePath);
            }
        }
    }
};
