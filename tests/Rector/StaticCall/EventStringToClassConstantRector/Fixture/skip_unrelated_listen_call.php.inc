<?php

namespace RectorLaravel\Tests\Rector\StaticCall\EventStringToClassConstantRector\Fixture;

class SomeSocket
{
    public function listen(string $channel): void
    {
    }
}

function listenOnSocket(SomeSocket $socket)
{
    $socket->listen('auth.login');
}

?>
