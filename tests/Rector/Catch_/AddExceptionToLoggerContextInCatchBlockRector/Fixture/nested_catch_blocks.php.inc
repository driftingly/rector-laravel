<?php

namespace RectorLaravel\Tests\Rector\Catch_\AddExceptionToLoggerContextInCatchBlockRector\Fixture;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

final class Nested
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function run(): void
    {
        try {
            $this->import();
        } catch (\RuntimeException $outer) {
            try {
                $this->retry();
            } catch (\LogicException $inner) {
                $this->logger->error('Retry failed');
            }
            $this->logger->error('Import failed');
        } catch (\Exception $other) {
            $this->logger->warning('Unknown');
        }
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Catch_\AddExceptionToLoggerContextInCatchBlockRector\Fixture;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

final class Nested
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function run(): void
    {
        try {
            $this->import();
        } catch (\RuntimeException $outer) {
            try {
                $this->retry();
            } catch (\LogicException $inner) {
                $this->logger->error('Retry failed', ['exception' => $inner]);
            }
            $this->logger->error('Import failed', ['exception' => $outer]);
        } catch (\Exception $other) {
            $this->logger->warning('Unknown', ['exception' => $other]);
        }
    }
}

?>
