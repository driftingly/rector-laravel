<?php

declare(strict_types=1);

namespace Illuminate\Routing;

if (class_exists('Illuminate\Routing\ResponseFactory')) {
    return;
}

class ResponseFactory implements \Illuminate\Contracts\Routing\ResponseFactory
{
}
