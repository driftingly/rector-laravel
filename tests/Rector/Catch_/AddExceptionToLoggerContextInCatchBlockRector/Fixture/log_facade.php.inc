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
            Log::error('Import failed');
            Log::warning('Import failed', ['file' => 'a.csv']);
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
            Log::error('Import failed', ['exception' => $e]);
            Log::warning('Import failed', ['file' => 'a.csv', 'exception' => $e]);
        }
    }
}

?>
