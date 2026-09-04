<?php

namespace RectorLaravel\Tests\Rector\Expr\ModelComparisonToIsMethodRector\Fixture;

class User
{
    public $id;
    public $email;
}

class Team
{
    public $user_id;
    public $name;
}

// These should NOT be transformed - not following the pattern
$team->name === $user->email;
$team->user_id === $user->email;
$team->name === $user->id;
$someVar === $user->id;
$team->user_id === $someVar;

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\ModelComparisonToIsMethodRector\Fixture;

class User
{
    public $id;
    public $email;
}

class Team
{
    public $user_id;
    public $name;
}

// These should NOT be transformed - not following the pattern
$team->name === $user->email;
$team->user_id === $user->email;
$team->name === $user->id;
$someVar === $user->id;
$team->user_id === $someVar;

?>
