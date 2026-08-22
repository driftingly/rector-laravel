<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Sets\ComposerBased;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

/**
 * The "composer.json" next to this test requires Laravel 12, so the Laravel 12 set and every set below it
 * must apply, while the Laravel 13 set and the sets of packages that are not required must not.
 */
final class ComposerBasedTest extends AbstractRectorTestCase
{
    public static function provideData(): Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    /**
     * @test
     */
    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    protected function provideComposerJsonFilePath(): string
    {
        return __DIR__ . '/composer.json';
    }
}
