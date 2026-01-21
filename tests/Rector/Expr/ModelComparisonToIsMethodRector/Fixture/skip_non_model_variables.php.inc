<?php

namespace RectorLaravel\Tests\Rector\Expr\ModelComparisonToIsMethodRector\Fixture;

class User
{
    public $id;
}

class Team
{
    public $user_id;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// Should NOT be transformed - non-model variables
$team = new Team();
$user = new User();
$index = 0;
$data = ['key' => 'value'];

$team->user_id = $user->id;
$team->user_id === $index;
$post->author_id == $data;

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\ModelComparisonToIsMethodRector\Fixture;

class User
{
    public $id;
}

class Team
{
    public $user_id;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// Should NOT be transformed - non-model variables
$team = new Team();
$user = new User();
$index = 0;
$data = ['key' => 'value'];

$team->user_id = $user->id;
$team->user_id === $index;
$post->author_id == $data;

?>
