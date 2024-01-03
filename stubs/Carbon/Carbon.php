<?php

namespace Carbon;

use DateTime;

if (class_exists('Carbon\Carbon')) {
    return;
}

class Carbon extends DateTime
{
    public static function now(): self
    {
        return new self;
    }

    public static function today(): self
    {
        return new self;
    }

    public function subDays(int $days): self
    {
        return $this;
    }
}
