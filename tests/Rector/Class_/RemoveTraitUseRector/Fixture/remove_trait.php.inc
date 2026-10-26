<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveTraitUseRector\Fixture;

use RectorLaravel\Tests\Rector\Class_\RemoveTraitUseRector\Source\SomeTrait;

class RemoveTrait
{
    use SomeTrait;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveTraitUseRector\Fixture;

use RectorLaravel\Tests\Rector\Class_\RemoveTraitUseRector\Source\SomeTrait;

class RemoveTrait
{
}

?>
