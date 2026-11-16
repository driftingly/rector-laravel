<?php

namespace RectorLaravel\Tests\Rector\Catch_\AddExceptionToLoggerContextInCatchBlockRector\Fixture;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

final class SomeService
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function run(): void
    {
        try {
            $this->import();
        } catch (\Exception $e) {
            $this->logger->error('Import failed');
            $this->logger->critical($e->getMessage());
        }
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Catch_\AddExceptionToLoggerContextInCatchBlockRector\Fixture;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

final class SomeService
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function run(): void
    {
        try {
            $this->import();
        } catch (\Exception $e) {
            $this->logger->error('Import failed', ['exception' => $e]);
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
        }
    }
}

?>
