<?php

declare(strict_types=1);

use Tracy\Dumper;

// helpful functions to create new rector rules

if (! function_exists('dd')) {
    function dd(mixed $value, int $depth = 2): never
    {
        d($value, $depth);
        exit;
    }
}

if (! function_exists('d')) {
    function d(mixed $value, int $depth = 2): void
    {
        Dumper::dump($value, [
            Dumper::DEPTH => $depth,
        ]);
    }
}
