<?php

declare(strict_types=1);

namespace Rector\Laravel\Tests\Rector\StaticCall\RouteActionCallableRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class RouteActionCallableRectorTest extends AbstractRectorTestCase
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
        return $this->yieldFilePathsFromDirectory(__DIR__ . '/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
