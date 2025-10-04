<?php

namespace RectorLaravel\Tests\Rector\FuncCall\AbortIfToGateDenyIfRector\Fixture;

class Fixture
{
    public function run($user, $post)
    {
        abort_if($user->id === $post->user_id);
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\AbortIfToGateDenyIfRector\Fixture;

class Fixture
{
    public function run($user, $post)
    {
        \Illuminate\Support\Facades\Gate::denyIf($user->id === $post->user_id);
    }
}

?>
