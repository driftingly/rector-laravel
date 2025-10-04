<?php

namespace RectorLaravel\Tests\Rector\FuncCall\AbortIfToGateDenyIfRector\Fixture;

class WithAuthHelper
{
    public function run($post)
    {
        abort_if(auth()->id() !== $post->author_id, 403);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\AbortIfToGateDenyIfRector\Fixture;

class WithAuthHelper
{
    public function run($post)
    {
        \Illuminate\Support\Facades\Gate::denyIf(auth()->id() !== $post->author_id, 403);
    }
}

?>
