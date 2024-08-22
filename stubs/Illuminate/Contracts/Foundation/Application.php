<?php

declare(strict_types=1);

namespace Illuminate\Contracts\Foundation;

use Illuminate\Contracts\Container\Container;

if (class_exists('Illuminate\Contracts\Foundation\Application')) {
    return;
}

interface Application extends Container
{
    public function tagged(string $tagName): iterable;

    public function storagePath($path = '');
}
