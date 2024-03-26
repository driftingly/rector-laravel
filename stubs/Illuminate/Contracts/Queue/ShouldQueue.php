<?php

namespace Illuminate\Contracts\Queue;

if (class_exists('Illuminate\Contracts\Queue\ShouldQueue')) {
    return;
}

interface ShouldQueue
{
}