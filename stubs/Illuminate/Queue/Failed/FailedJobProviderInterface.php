<?php

declare(strict_types=1);

namespace Illuminate\Queue\Failed;

if (class_exists('Illuminate\Queue\Failed\FailedJobProviderInterface')) {
    return;
}

interface FailedJobProviderInterface
{
}
