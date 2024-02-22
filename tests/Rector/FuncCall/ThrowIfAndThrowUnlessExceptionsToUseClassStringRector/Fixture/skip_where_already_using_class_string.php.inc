<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ThrowIfAndThrowUnlessExceptionsToUseClassStringRector\Fixture;

throw_if(true, \Exception::class, 'message');

?>
