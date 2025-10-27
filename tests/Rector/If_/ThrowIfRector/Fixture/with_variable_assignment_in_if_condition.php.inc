<?php

namespace RectorLaravel\Tests\Rector\If_\ThrowIfRector\Fixture;

if ($var = 5) {
    throw new \Exception($var);
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\If_\ThrowIfRector\Fixture;

throw_if($var = 5, new \Exception($var));

?>
