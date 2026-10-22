<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayAccessToMethodCallRector\Fixture;

use Illuminate\Contracts\Config\Repository;

function readConfig(Repository $config): void
{
    echo $config['app.name'];
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ArrayAccessToMethodCallRector\Fixture;

use Illuminate\Contracts\Config\Repository;

function readConfig(Repository $config): void
{
    echo $config->get('app.name');
}

?>
