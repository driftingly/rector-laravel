<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\Packages\Cashier\CashierSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([CashierSetList::LARAVEL_CASHIER_130]);
};
