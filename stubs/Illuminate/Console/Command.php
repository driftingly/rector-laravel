<?php

declare(strict_types=1);

namespace Illuminate\Console;

if (class_exists('Illuminate\Console\Command')) {
    return;
}

class Command
{
    protected $signature;

    protected $description;

    protected $aliases = [];

    protected $help = '';

    protected $hidden = false;

    public function handle(): void {}
}
