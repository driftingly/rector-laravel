<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;

// see https://github.com/laravel/cashier-stripe/blob/master/UPGRADE.md#upgrading-to-140-from-13x
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../config.php');

    $rectorConfig->ruleWithConfiguration(RenameMethodRector::class, [
        new MethodCallRename('Laravel\Cashier\Billable', 'removePaymentMethod', 'deletePaymentMethod'),
        new MethodCallRename('Laravel\Cashier\Payment', 'isCancelled', 'isCanceled'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'cancelled', 'canceled'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'markAsCancelled', 'markAsCanceled'),
    ]);
};
