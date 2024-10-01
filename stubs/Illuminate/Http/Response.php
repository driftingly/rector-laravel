<?php

namespace Illuminate\Http;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

if (class_exists('Illuminate\Http\Response')) {
    return;
}

class Response extends SymfonyResponse
{
}
