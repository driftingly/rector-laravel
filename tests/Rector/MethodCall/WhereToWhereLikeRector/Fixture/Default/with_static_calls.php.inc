<?php

namespace RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Source\Post;

class WithStaticCalls
{
    public function run()
    {
        Post::where('name', 'like', 'Rector');
        Post::orWhere('name', 'like', 'Rector');
        Post::orwhere('name', 'LIKE', 'Rector');
        Post::where('name', 'not like', 'Rector');
        Post::orwhere('name', 'not like', 'Rector');
        Post::orwhere('name', like: 'not like', value: 'Rector');

        // Case Sensitivity
        Post::where('name', 'like binary', 'Rector');
        Post::where('name', 'not like binary', 'Rector');
        Post::where('name', like: 'like binary', value: 'Rector');
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Fixture;

use RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Source\Post;

class WithStaticCalls
{
    public function run()
    {
        Post::whereLike('name', 'Rector');
        Post::orWhereLike('name', 'Rector');
        Post::orWhereLike('name', 'Rector');
        Post::whereNotLike('name', 'Rector');
        Post::orWhereNotLike('name', 'Rector');
        Post::orWhereNotLike('name', value: 'Rector');

        // Case Sensitivity
        Post::whereLike('name', 'Rector', true);
        Post::whereNotLike('name', 'Rector', true);
        Post::whereLike('name', value: 'Rector', caseSensitive: true);
    }
}
?>
