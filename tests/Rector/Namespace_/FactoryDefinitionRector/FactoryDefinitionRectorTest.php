<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\Namespace_\FactoryDefinitionRector;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class FactoryDefinitionRectorTest extends AbstractRectorTestCase
{
    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public static function provideData(): Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}