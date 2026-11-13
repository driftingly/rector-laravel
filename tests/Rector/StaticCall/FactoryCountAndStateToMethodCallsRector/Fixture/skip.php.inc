<?php

namespace RectorLaravel\Tests\Rector\StaticCall\FactoryCountAndStateToMethodCallsRector\Fixture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
}

class Post extends Model
{
    use HasFactory;

    public static function seed(): void
    {
        static::factory(3);
        self::factory(3);
    }
}

class Comment extends Post
{
    public static function seedParent(): void
    {
        parent::factory(3);
    }
}

class WithoutTrait
{
    public static function factory($count = null, $state = []) {}
}

function returnsNull(): null
{
    return null;
}

function skip(mixed $mixed, int $int, array $states, User $model)
{
    User::factory();
    User::factory()->count(3);
    User::factory(config('x'));
    User::factory($mixed);
    User::factory(...$states);
    User::factory('3');
    User::factory(3, ...$states);
    User::factory(count: $mixed);
    User::factory(returnsNull(), ['name' => 'Taylor']);
    User::factory(['name' => 'Taylor'], ['name' => 'Otwell']);
    $model::factory(3);
    WithoutTrait::factory(3);
    WithoutTrait::factory(['name' => 'Taylor']);
    $user = new User();
    $user->factory(3);
}
