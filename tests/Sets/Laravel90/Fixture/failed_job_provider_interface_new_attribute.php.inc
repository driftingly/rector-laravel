<?php

namespace RectorLaravel\Tests\Sets\Laravel90;

use Illuminate\Queue\Failed\FailedJobProviderInterface;

class FailedJobProvider implements FailedJobProviderInterface
{
    public function flush($age = null)
    {
    }
}

$provider = new FailedJobProvider();
$provider->flush();
$provider->flush(10);

?>
