<?php

namespace RectorLaravel\Tests\Rector\Catch_\AddExceptionToLoggerContextInCatchBlockRector\Fixture;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

final class NotInCatch
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function run(): void
    {
        $this->logger->error('Import failed');
    }
}

?>
