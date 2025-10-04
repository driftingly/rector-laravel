<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\StaticCall\CarbonToDateFacadeRector;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Exception\ShouldNotHappenException;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class CarbonToDateFacadeRectorTest extends AbstractRectorTestCase
{
    public static function provideData(): Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    /**
     * @test
     *
     * @throws ShouldNotHappenException
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
}
