<?php

namespace RectorLaravel\Tests\Rector\New_\NewInstanceToAppMakeRector\Fixture;

new \SomeNamespace\SomeClass();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\New_\NewInstanceToAppMakeRector\Fixture;

\Illuminate\Support\Facades\App::make(\SomeNamespace\SomeClass::class);

?>
