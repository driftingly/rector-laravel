<?php

namespace RectorLaravel\Tests\Rector\FuncCall\TypeHintTappableCallRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\TypeHintTappableCallRector\Source\TappableExample;

tap('test');

$example = new TappableExample();

$example->tap();

?>
