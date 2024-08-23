<?php

namespace RectorLaravel\Tests\Sets\Laravel90\Fixture;

use Illuminate\Queue\Failed\FailedJobProviderInterface;

class FailedJobProvider implements FailedJobProviderInterface
{
    public function flush($age = null)
    {
    }

    public function log($connection, $queue, $payload, $exception)
    {
    }

    public function all()
    {
    }

    public function find($id)
    {
    }

    public function forget($id)
    {
    }
}

$provider = new FailedJobProvider();
$provider->flush();
$provider->flush(10);

?>
