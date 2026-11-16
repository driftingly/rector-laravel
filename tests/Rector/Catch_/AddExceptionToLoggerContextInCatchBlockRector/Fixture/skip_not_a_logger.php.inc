<?php

namespace RectorLaravel\Tests\Rector\Catch_\AddExceptionToLoggerContextInCatchBlockRector\Fixture;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

final class Output
{
    public function error(string $message): void
    {
    }
}

final class SomeService
{
    public function __construct(private readonly Output $output)
    {
    }

    public function run(): void
    {
        try {
            $this->import();
        } catch (\Exception $e) {
            $this->output->error('Import failed');
        }
    }
}

?>
