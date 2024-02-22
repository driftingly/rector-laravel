<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ThrowIfAndThrowUnlessExceptionsToUseClassStringRector\Fixture;

throw_if(true, new \Exception('message'));
throw_unless(false, new \Exception('message', 'code'));

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ThrowIfAndThrowUnlessExceptionsToUseClassStringRector\Fixture;

throw_if(true, \Exception::class, 'message');
throw_unless(false, \Exception::class, 'message', 'code');

?>
