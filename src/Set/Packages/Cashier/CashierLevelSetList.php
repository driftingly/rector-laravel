<?php

declare(strict_types=1);

namespace RectorLaravel\Set\Packages\Cashier;

use Rector\Set\Contract\SetListInterface;

final class CashierLevelSetList implements SetListInterface
{
    /**
     * @var string
     */
    final public const UP_TO_LARAVEL_CASHIER_13 = __DIR__ . '/../../config/sets/packages/cashier/level/up-to-cashier-13.php';

    /**
     * @var string
     */
    final public const UP_TO_LARAVEL_CASHIER_14 = __DIR__ . '/../../config/sets/packages/cashier/level/up-to-cashier-14.php';
}
