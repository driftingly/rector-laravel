<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ReplaceServiceContainerCallArgRector\Fixture;

resolve('encrypter')->encrypt('hello world');
resolve(\Illuminate\Contracts\Session\Session::class)->get('hello world');

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\ReplaceServiceContainerCallArgRector\Fixture;

resolve(\Illuminate\Contracts\Encryption\Encrypter::class)->encrypt('hello world');
resolve('session')->get('hello world');

?>
