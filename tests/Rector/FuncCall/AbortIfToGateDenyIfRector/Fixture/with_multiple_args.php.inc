<?php

namespace RectorLaravel\Tests\Rector\FuncCall\AbortIfToGateDenyIfRector\Fixture;

class WithMultipleArgs
{
    public function run($post)
    {
        abort_if($post->user()->is(auth()->user()), 403, 'Unauthorized');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\AbortIfToGateDenyIfRector\Fixture;

class WithMultipleArgs
{
    public function run($post)
    {
        \Illuminate\Support\Facades\Gate::denyIf($post->user()->is(auth()->user()), 403, 'Unauthorized');
    }
}

?>
