<?php

declare(strict_types=1);

namespace Illuminate\Queue\Failed;

if (class_exists('Illuminate\Queue\Failed\FailedJobProviderInterface')) {
    return;
}

interface FailedJobProviderInterface
{
    public function log($connection, $queue, $payload, $exception);

    public function all();

    public function find($id);

    public function forget($id);

    public function flush($hours = null);
}
