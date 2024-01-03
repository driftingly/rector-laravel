<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use RectorLaravel\Rector\Class_\CashierStripeOptionsToStripeRector;

// see https://github.com/laravel/cashier-stripe/blob/master/UPGRADE.md#upgrading-to-130-from-12x
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../config.php');

    $rectorConfig->ruleWithConfiguration(RenameMethodRector::class, [
        new MethodCallRename('Laravel\Cashier\Billable', 'subscribedToPlan', 'subscribedToPrice'),
        new MethodCallRename('Laravel\Cashier\Billable', 'onPlan', 'onPrice'),
        new MethodCallRename('Laravel\Cashier\Billable', 'planTaxRates', 'priceTaxRates'),
        new MethodCallRename('Laravel\Cashier\SubscriptionBuilder', 'plan', 'price'),
        new MethodCallRename('Laravel\Cashier\SubscriptionBuilder', 'meteredPlan', 'meteredPrice'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'hasMultiplePlans', 'hasMultiplePrices'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'hasSinglePlan', 'hasSinglePrice'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'hasPlan', 'hasPrice'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'addPlan', 'addPrice'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'addPlanAndInvoice', 'addPriceAndInvoice'),
        new MethodCallRename('Laravel\Cashier\Subscription', 'removePlan', 'removePrice'),
    ]);

    $rectorConfig->rule(CashierStripeOptionsToStripeRector::class);
};
