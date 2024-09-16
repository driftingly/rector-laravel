<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\MethodCall\ResponseHelperCallToJsonResponseRector;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class ResponseHelperCallToJsonResponseRectorTest extends AbstractRectorTestCase
{
    public static function provideData(): Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    #[Test]
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
