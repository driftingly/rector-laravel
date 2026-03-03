<?php

namespace RectorLaravel\Tests\Rector\Expr\ModelComparisonToIsMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $id;
}

class Team extends Model
{
    public $user_id;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

class Post extends Model
{
    public $author_id;

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}

// Basic comparison cases
$team->user_id === $user->id;
$team->user_id === $user->id;
$post->author_id === $author->id;
$post->author_id === $author->id;

// Reversed comparison
$user->id === $team->user_id;
$author->id === $post->author_id;

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\ModelComparisonToIsMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $id;
}

class Team extends Model
{
    public $user_id;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

class Post extends Model
{
    public $author_id;

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}

// Basic comparison cases
$team->user()->is($user);
$team->user()->is($user);
$post->author()->is($author);
$post->author()->is($author);

// Reversed comparison
$team->user()->is($user);
$post->author()->is($author);

?>
