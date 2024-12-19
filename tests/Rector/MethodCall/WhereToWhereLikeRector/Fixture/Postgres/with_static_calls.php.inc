<?php

namespace RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Fixture\Postgres;

use RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Source\Post;

class WithPostgresDriverStaticCalls
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
        Post::where('name', 'ilike', 'Rector');
        Post::where('name', 'not ilike', 'Rector');
        Post::where('name', like: 'ilike', value: 'Rector');

        // Invalid
        NonModel::where('name', 'like', 'Rector');
    }
}
?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Fixture\Postgres;

use RectorLaravel\Tests\Rector\MethodCall\WhereToWhereLikeRector\Source\Post;

class WithPostgresDriverStaticCalls
{
    public function run()
    {
        Post::whereLike('name', 'Rector', true);
        Post::orWhereLike('name', 'Rector', true);
        Post::orWhereLike('name', 'Rector', true);
        Post::whereNotLike('name', 'Rector', true);
        Post::orWhereNotLike('name', 'Rector', true);
        Post::orWhereNotLike('name', value: 'Rector', caseSensitive: true);

        // Case Sensitivity
        Post::whereLike('name', 'Rector');
        Post::whereNotLike('name', 'Rector');
        Post::whereLike('name', value: 'Rector');

        // Invalid
        NonModel::where('name', 'like', 'Rector');
    }
}
?>
