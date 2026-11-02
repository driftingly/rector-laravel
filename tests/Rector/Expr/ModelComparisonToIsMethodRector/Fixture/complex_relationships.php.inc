<?php

namespace RectorLaravel\Tests\Rector\Expr\ModelComparisonToIsMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $post_id;
    public $author_id;

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}

class Post extends Model
{
    public $id;
    public $author_id;

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}

class User extends Model
{
    public $id;
}

// Complex relationship comparisons
$comment->post_id === $post->id;
$comment->author_id === $author->id;
$post->author_id === $user->id;

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\ModelComparisonToIsMethodRector\Fixture;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $post_id;
    public $author_id;

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}

class Post extends Model
{
    public $id;
    public $author_id;

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}

class User extends Model
{
    public $id;
}

// Complex relationship comparisons
$comment->post()->is($post);
$comment->author()->is($author);
$post->author()->is($user);

?>
