<?php

declare(strict_types=1);

namespace Rector\Laravel\Tests\Rector\Class_\AnonymousMigrationsRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class AnonymousMigrationsRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
