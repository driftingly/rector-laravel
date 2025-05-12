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
        'Laravel Framework 12.0' => LaravelSetList::LARAVEL_120,
        'Laravel Framework 11.0' => LaravelSetList::LARAVEL_110,
        'Laravel Framework 10.0' => LaravelSetList::LARAVEL_100,
        'Laravel Framework 9.0' => LaravelSetList::LARAVEL_90,
        'Laravel Framework 8.0' => LaravelSetList::LARAVEL_80,
        'Laravel Framework 7.0' => LaravelSetList::LARAVEL_70,
        'Laravel Framework 6.0' => LaravelSetList::LARAVEL_60,
        'Laravel Framework 5.8' => LaravelSetList::LARAVEL_58,
        'Laravel Framework 5.7' => LaravelSetList::LARAVEL_57,
        'Laravel Framework 5.6' => LaravelSetList::LARAVEL_56,
        'Laravel Framework 5.5' => LaravelSetList::LARAVEL_55,
        'Laravel Framework 5.4' => LaravelSetList::LARAVEL_54,
        'Laravel Framework 5.3' => LaravelSetList::LARAVEL_53,
        'Laravel Framework 5.2' => LaravelSetList::LARAVEL_52,
        'Laravel Framework 5.1' => LaravelSetList::LARAVEL_51,
        'Laravel Framework 5.0' => LaravelSetList::LARAVEL_50,
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

        Assert::assertSame(array_values(self::LARAVEL_VERSION_SETS), array_values($filePaths));

        $setNames = array_filter(
            array_map(
                fn (SetInterface $set) => $set->getName(),
                $sets
            ),
            fn (string $setName) => in_array($setName, array_keys(self::LARAVEL_VERSION_SETS), true),
        );

        Assert::assertSame(array_keys(self::LARAVEL_VERSION_SETS), array_values($setNames));
    }
}
