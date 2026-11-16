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
            $this->logger->emergency('a');
            $this->logger->alert('a');
            $this->logger->critical('a');
            $this->logger->error('a');
            $this->logger->warning('a');
            $this->logger->notice('a');
            $this->logger->info('a');
            $this->logger->debug('a');
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
            $this->logger->emergency('a', ['exception' => $e]);
            $this->logger->alert('a', ['exception' => $e]);
            $this->logger->critical('a', ['exception' => $e]);
            $this->logger->error('a', ['exception' => $e]);
            $this->logger->warning('a', ['exception' => $e]);
            $this->logger->notice('a', ['exception' => $e]);
            $this->logger->info('a', ['exception' => $e]);
            $this->logger->debug('a', ['exception' => $e]);
        }
    }
}

?>
