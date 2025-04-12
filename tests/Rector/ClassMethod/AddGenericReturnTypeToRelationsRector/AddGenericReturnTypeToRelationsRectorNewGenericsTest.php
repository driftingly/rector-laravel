<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use RectorLaravel\Tests\Support\InteractsWithLaravelVersion;

final class AddGenericReturnTypeToRelationsRectorNewGenericsTest extends AbstractRectorTestCase
{
    use InteractsWithLaravelVersion;

    public static function provideData(): Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/Fixture/NewGenerics');
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

    public function version(): string
    {
        return '12.3.0';
    }
}
