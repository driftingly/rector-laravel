<?php

namespace RectorLaravel\Set;

use Rector\Set\Contract\SetInterface;
use Rector\Set\Contract\SetProviderInterface;
use Rector\Set\ValueObject\Set;
use RectorLaravel\Set\Packages\Livewire\LivewireSetList;

final class LaravelSetProvider implements SetProviderInterface
{
    /**
     * @var string
     */
    private const GROUP_NAME = 'laravel';

    /**
     * @var string[]
     */
    private const LARAVEL_FIVE = [
        LaravelSetList::LARAVEL_58,
        LaravelSetList::LARAVEL_57,
        LaravelSetList::LARAVEL_56,
        LaravelSetList::LARAVEL_55,
        LaravelSetList::LARAVEL_54,
        LaravelSetList::LARAVEL_53,
        LaravelSetList::LARAVEL_52,
        LaravelSetList::LARAVEL_51,
        LaravelSetList::LARAVEL_50,
    ];

    /**
     * @var string[]
     */
    private const LARAVEL_POST_FIVE = [
        LaravelSetList::LARAVEL_120,
        LaravelSetList::LARAVEL_110,
        LaravelSetList::LARAVEL_100,
        LaravelSetList::LARAVEL_90,
        LaravelSetList::LARAVEL_80,
        LaravelSetList::LARAVEL_70,
        LaravelSetList::LARAVEL_60,
    ];

    /**
     * @return SetInterface[]
     */
    public function provide(): array
    {
        return array_merge([new Set(
            self::GROUP_NAME,
            'Code quality',
            LaravelSetList::LARAVEL_CODE_QUALITY
        ), new Set(
            self::GROUP_NAME,
            'Collection improvements and simplifications',
            LaravelSetList::LARAVEL_COLLECTION,
        ), new Set(
            self::GROUP_NAME,
            'Container array access to method calls',
            LaravelSetList::LARAVEL_ARRAYACCESS_TO_METHOD_CALL,
        ), new Set(
            self::GROUP_NAME,
            'Container strings to FQN types',
            LaravelSetList::LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME,
        ), new Set(
            self::GROUP_NAME,
            'Rename Aliases to FQN Classes',
            LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
        ), new Set(
            self::GROUP_NAME,
            'Replace array/str functions with static calls',
            LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL
        ), new Set(
            self::GROUP_NAME,
            'Replace If statements with helpers',
            LaravelSetList::LARAVEL_IF_HELPERS,
        ), new Set(
            self::GROUP_NAME,
            'Replace facades with service injection',
            LaravelSetList::LARAVEL_STATIC_TO_INJECTION,
        ), new Set(
            self::GROUP_NAME,
            'Replace Magic Methods with Query Builder',
            LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
        ), new Set(
            self::GROUP_NAME,
            'Upgrade Legacy Factories to Modern Factories',
            LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
        ), new Set(
            self::GROUP_NAME,
            'Livewire 3.0',
            LivewireSetList::LIVEWIRE_30,
        )], $this->getLaravelVersions());
    }

    /**
     * @return Set[]
     */
    private function getLaravelVersions(): array
    {
        $versions = [];

        foreach (self::LARAVEL_POST_FIVE as $index => $version) {
            $versions[] = new Set(
                self::GROUP_NAME,
                'Laravel Framework ' . ($index + 6) . '.0',
                $version,
            );
        }

        foreach (self::LARAVEL_FIVE as $index => $version) {
            $versions[] = new Set(
                self::GROUP_NAME,
                'Laravel Framework 5.' . $index,
                $version,
            );
        }

        return $versions;
    }
}
