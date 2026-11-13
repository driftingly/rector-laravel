<?php

namespace RectorLaravel\Tests\Rector\StaticCall\FactoryCountAndStateToMethodCallsRector\Fixture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
}

class Admin extends User {}

User::factory(3);
User::factory(['name' => 'Taylor']);
User::factory(fn (array $attributes) => ['name' => 'Taylor']);
User::factory(3, ['name' => 'Taylor']);
User::factory(null, ['name' => 'Taylor']);
User::factory(count: 3);
User::factory(state: ['name' => 'Taylor']);
User::factory(state: ['name' => 'Taylor'], count: 3);
Admin::factory(3);

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\FactoryCountAndStateToMethodCallsRector\Fixture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
}

class Admin extends User {}

User::factory()->count(3);
User::factory()->state(['name' => 'Taylor']);
User::factory()->state(fn (array $attributes) => ['name' => 'Taylor']);
User::factory()->count(3)->state(['name' => 'Taylor']);
User::factory()->state(['name' => 'Taylor']);
User::factory()->count(3);
User::factory()->state(['name' => 'Taylor']);
User::factory()->state(['name' => 'Taylor'])->count(3);
Admin::factory()->count(3);

?>
