<?php

namespace RectorLaravel\Tests\Rector\Catch_\AddExceptionToLoggerContextInCatchBlockRector\Fixture;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

abstract class BufferedLogger implements LoggerInterface
{
    abstract public function flush(): void;
}

final class SomeService
{
    public function __construct(private readonly BufferedLogger $logger)
    {
    }

    public function run(): void
    {
        try {
            $this->import();
        } catch (\Exception $e) {
            $this->logger->flush();
        }
    }
}

?>
