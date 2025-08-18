<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class AddUseAnnotationToHasFactoryTraitRectorTest extends AbstractRectorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        require_once __DIR__ . '/Factories/ProductFactory.php';
        require_once __DIR__ . '/Factories/UserFactory.php';
        require_once __DIR__ . '/Factories/Tenant/UserFactory.php';
    }

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
}
