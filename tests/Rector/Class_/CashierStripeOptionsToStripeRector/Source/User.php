<?php

namespace RectorLaravel\Tests\Rector\Class_\CashierStripeOptionsToStripeRector\Source;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class User extends Model
{
    use Billable;
}
