<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveTraitUseRector\Fixture;

use RectorLaravel\Tests\Rector\Class_\RemoveTraitUseRector\Source\OtherTrait;
use RectorLaravel\Tests\Rector\Class_\RemoveTraitUseRector\Source\SomeTrait;

trait KeepOtherTraits
{
    use SomeTrait, OtherTrait;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveTraitUseRector\Fixture;

use RectorLaravel\Tests\Rector\Class_\RemoveTraitUseRector\Source\OtherTrait;
use RectorLaravel\Tests\Rector\Class_\RemoveTraitUseRector\Source\SomeTrait;

trait KeepOtherTraits
{
    use OtherTrait;
}

?>
