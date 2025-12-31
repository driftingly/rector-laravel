<?php

namespace RectorLaravel\Tests\Rector\Expr\ModelComparisonToIsMethodRector\Fixture;

class User
{
    public $id;
}

class Post
{
    public $author_id;
    public $editor_id;

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class);
    }
}

class Comment
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

// Multiple relationships in one function
function checkOwnership($post, $comment, $user) {
    if ($post->author_id === $user->id && $comment->author_id === $user->id) {
        return true;
    }

    return $post->editor_id === $user->id || $comment->post_id === $post->id;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Expr\ModelComparisonToIsMethodRector\Fixture;

class User
{
    public $id;
}

class Post
{
    public $author_id;
    public $editor_id;

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class);
    }
}

class Comment
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

// Multiple relationships in one function
function checkOwnership($post, $comment, $user) {
    if ($post->author()->is($user) && $comment->author()->is($user)) {
        return true;
    }

    return $post->editor()->is($user) || $comment->post()->is($post);
}

?>
