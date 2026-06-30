<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\MergeExistingObservedByAttribute;

use Illuminate\Foundation\Auth\User as Authenticatable;

#[\Illuminate\Database\Eloquent\Attributes\ObservedBy([\RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\MergeExistingObservedByAttribute\ExistingObserver::class])]
class User extends Authenticatable
{
}

final class ExistingObserver
{
}

?>
-----
<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\MergeExistingObservedByAttribute;

use Illuminate\Foundation\Auth\User as Authenticatable;

#[\Illuminate\Database\Eloquent\Attributes\ObservedBy([\RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\MergeExistingObservedByAttribute\ExistingObserver::class, \RectorLaravel\Tests\Rector\Class_\ObserveCallsToObservedByAttributeRector\Fixture\MergeExistingObservedByAttribute\UserObserver::class])]
class User extends Authenticatable
{
}

final class ExistingObserver
{
}

?>
