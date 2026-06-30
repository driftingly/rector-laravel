<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Sets\Laravel130\Fixture\ObservedByAttribute;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
}

?>
-----
<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Sets\Laravel130\Fixture\ObservedByAttribute;

use Illuminate\Foundation\Auth\User as Authenticatable;

#[\Illuminate\Database\Eloquent\Attributes\ObservedBy([\RectorLaravel\Tests\Sets\Laravel130\Fixture\ObservedByAttribute\UserObserver::class])]
class User extends Authenticatable
{
}

?>
