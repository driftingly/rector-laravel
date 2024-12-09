<?php

namespace Laravel\Cashier;

if (class_exists('Laravel\Cashier\Billable')) {
    return;
}

trait Billable {}
