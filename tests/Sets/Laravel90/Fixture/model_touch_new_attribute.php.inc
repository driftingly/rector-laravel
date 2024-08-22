<?php

namespace RectorLaravel\Tests\Sets\Laravel90\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function touch()
    {
        return true;
    }
}

$user = new User();
$user->touch();

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel90\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function touch($attribute = null)
    {
        return true;
    }
}

$user = new User();
$user->touch();

?>
