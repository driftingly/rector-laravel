<?php

namespace Rector\Laravel\Tests\Rector\Class_\AnonymousMigrationsRector\Fixture;

use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    // ...
}

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\Class_\AnonymousMigrationsRector\Fixture;

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    // ...
};

?>
