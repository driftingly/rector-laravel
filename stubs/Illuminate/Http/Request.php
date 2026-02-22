<?php

declare(strict_types=1);

namespace Illuminate\Http;

if (class_exists('Illuminate\Http\Request')) {
    return;
}

class Request {}
