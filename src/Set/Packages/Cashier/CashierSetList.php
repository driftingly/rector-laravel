<?php

declare(strict_types=1);

namespace RectorLaravel\Set\Packages\Cashier;

use Rector\Set\Contract\SetListInterface;

final class CashierSetList implements SetListInterface
{
    /**
     * @var string
     */
    public const LARAVEL_CASHIER_130 = __DIR__ . '/../../../../config/sets/packages/cashier/cashier-13.php';

    /**
     * @var string
     */
    public const LARAVEL_CASHIER_140 = __DIR__ . '/../../../../config/sets/packages/cashier/cashier-14.php';
}
