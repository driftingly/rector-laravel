<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\StaticCall\Redirect301ToPermanentRedirectRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class Redirect301ToPermanentRedirectRectorTest extends AbstractRectorTestCase
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
