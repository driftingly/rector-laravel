<?php

declare(strict_types=1);

namespace RectorLaravel\Set\Packages\Cashier;

/**
 * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead, which registers the
 *             Cashier upgrade rules for the installed laravel/cashier version automatically.
 */
final class CashierLevelSetList
{
    public const string UP_TO_LARAVEL_CASHIER_130 = __DIR__ . '/../../../../config/sets/packages/cashier/level/up-to-cashier-13.php';

    public const string UP_TO_LARAVEL_CASHIER_140 = __DIR__ . '/../../../../config/sets/packages/cashier/level/up-to-cashier-14.php';
}
