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
        // mutate the shared singleton instance the rules inject, so the version does not
        // leak from another version-bound test class booted earlier in the same process
        self::getContainer()->make(ApplicationAnalyzer::class)
            ->setVersion($this->version());
    }

    abstract public function version(): string;
}
