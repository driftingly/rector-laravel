<?php

declare(strict_types=1);

namespace RectorLaravel\Set;

use Rector\Set\Contract\SetListInterface;

final class LaravelCashierSetList implements SetListInterface
{
    /**
     * @var string
     */
    final public const LARAVEL_CASHIER_130 = __DIR__ . '/../../config/sets/laravel-cashier-13.php';
}
