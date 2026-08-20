<?php

namespace RectorLaravel\Tests\Sets\ComposerBased\Fixture;

use Illuminate\Support\Facades\Bus;

Bus::dispatchNow(fn () => null);

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\ComposerBased\Fixture;

use Illuminate\Support\Facades\Bus;

Bus::dispatchSync(fn () => null);

?>
