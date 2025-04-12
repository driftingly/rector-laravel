<?php

namespace RectorLaravel\Tests\Support;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use RectorLaravel\NodeAnalyzer\ApplicationAnalyzer;

/**
 * @mixin AbstractRectorTestCase
 */
trait InteractsWithLaravelVersion
{
    /**
     * @before
     */
    public function setAppVersion(): void
    {
        self::getContainer()->singleton(
            ApplicationAnalyzer::class,
            fn () => (new ApplicationAnalyzer)
                ->setVersion($this->version())
        );
    }

    abstract public function version(): string;
}
