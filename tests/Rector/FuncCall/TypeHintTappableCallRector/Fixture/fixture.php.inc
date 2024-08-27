<?php

namespace RectorLaravel\Tests\Rector\FuncCall\TypeHintTappableCallRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\TypeHintTappableCallRector\Source\TappableExample;

$example = new TappableExample();

$example->tap(function ($example) {

});

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\TypeHintTappableCallRector\Fixture;

use RectorLaravel\Tests\Rector\FuncCall\TypeHintTappableCallRector\Source\TappableExample;

$example = new TappableExample();

$example->tap(function (\RectorLaravel\Tests\Rector\FuncCall\TypeHintTappableCallRector\Source\TappableExample $example) {

});

?>
