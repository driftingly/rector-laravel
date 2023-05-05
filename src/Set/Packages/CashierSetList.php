<?php

declare(strict_types=1);

namespace RectorLaravel\Set\Packages;

use Rector\Set\Contract\SetListInterface;

final class CashierSetList implements SetListInterface
{
    /**
     * @var string
     */
    final public const LARAVEL_CASHIER_130 = __DIR__ . '/../../config/sets/packages/cashier-13.php';
}
