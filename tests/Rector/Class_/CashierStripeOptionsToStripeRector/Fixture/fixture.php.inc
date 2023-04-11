<?php

namespace RectorLaravel\Tests\Rector\Class_\CashierStripeOptionsToStripeRector\Fixture;

use RectorLaravel\Tests\Rector\Class_\CashierStripeOptionsToStripeRector\Source\User;

class SomeUser extends User
{
    public function stripeOptions(array $options = []) {
        return [];
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\CashierStripeOptionsToStripeRector\Fixture;

use RectorLaravel\Tests\Rector\Class_\CashierStripeOptionsToStripeRector\Source\User;

class SomeUser extends User
{
    public function stripe(array $options = []) {
        return [];
    }
}

?>
