<?php

declare(strict_types=1);

namespace Illuminate\Foundation;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

if (class_exists('Illuminate\Foundation\Application')) {
    return;
}

class Application extends Container implements ApplicationContract
{
    const VERSION = '12.0.0';

    public function tagged(string $tagName): iterable
    {
        return [];
    }

    public function storagePath($path = '')
    {
        return $path;
    }

    public function langPath($path = '')
    {
        return $path;
    }
}
