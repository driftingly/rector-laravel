<?php

namespace RectorLaravel\Tests\Sets;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Rector\Set\Contract\SetInterface;
use RectorLaravel\Set\LaravelSetList;
use RectorLaravel\Set\LaravelSetProvider;

final class LaravelSetProviderTest extends TestCase
{
    private const array LARAVEL_VERSION_SETS = [
        LaravelSetList::LARAVEL_120,
        LaravelSetList::LARAVEL_110,
        LaravelSetList::LARAVEL_100,
        LaravelSetList::LARAVEL_90,
        LaravelSetList::LARAVEL_80,
        LaravelSetList::LARAVEL_70,
        LaravelSetList::LARAVEL_60,
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
     * @test
     */
    public function it_provides_sets(): void
    {
        $laravelSetProvider = new LaravelSetProvider;

        Assert::assertContainsOnlyInstancesOf(
            SetInterface::class,
            $laravelSetProvider->provide()
        );
    }

    /**
     * @test
     */
    public function it_returns_unique_sets(): void
    {
        $laravelSetProvider = new LaravelSetProvider;

        $sets = $laravelSetProvider->provide();

        $uniqueSets = array_unique(array_map(fn (SetInterface $set) => $set->getSetFilePath(), $sets));

        Assert::assertCount(count($sets), $uniqueSets);
    }

    /**
     * @test
     */
    public function it_provides_all_laravel_versions(): void
    {
        $laravelSetProvider = new LaravelSetProvider;

        $sets = $laravelSetProvider->provide();

        $filePaths = array_filter(
            array_map(
                fn (SetInterface $set) => $set->getSetFilePath(),
                $sets
            ),
            fn (string $filePath) => in_array($filePath, self::LARAVEL_VERSION_SETS, true),
        );

        Assert::assertSame(self::LARAVEL_VERSION_SETS, array_values($filePaths));
    }
}
