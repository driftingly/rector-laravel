<?php

namespace RectorLaravel\Tests\Rector\FuncCall\AbortIfToGateDenyIfRector\Fixture;

class SkipNonUserCondition
{
    public function run($condition)
    {
        abort_if($condition);
    }
}

?>
