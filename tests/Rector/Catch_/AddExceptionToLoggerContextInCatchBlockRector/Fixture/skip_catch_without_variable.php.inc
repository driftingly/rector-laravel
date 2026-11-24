<?php

namespace RectorLaravel\Tests\Rector\Catch_\AddExceptionToLoggerContextInCatchBlockRector\Fixture;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

final class NoVariable
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function run(): void
    {
        try {
            $this->import();
        } catch (\Exception) {
            $this->logger->error('Import failed');
        }
    }
}

?>
