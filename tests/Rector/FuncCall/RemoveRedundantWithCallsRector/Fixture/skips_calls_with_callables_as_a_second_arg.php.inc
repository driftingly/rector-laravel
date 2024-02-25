<?php

namespace RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantWithCallsRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\RemoveRedundantWithCallsRector\Source\HelperObject;

with(new HelperObject(), function () {

})->store();
