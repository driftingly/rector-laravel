<?php

namespace RectorLaravel\Tests\Rector\FuncCall\TypeHintTappableCallRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\TypeHintTappableCallRector\Source\NonTappableExample;

$example = new NonTappableExample();

$example->tap(function ($example) {

});

?>
