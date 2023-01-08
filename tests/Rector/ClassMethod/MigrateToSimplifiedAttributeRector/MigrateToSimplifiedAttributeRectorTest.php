<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\MigrateToSimplifiedAttributeRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class MigrateToSimplifiedAttributeRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    /**
     * @return Iterator<string>
     */
    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
