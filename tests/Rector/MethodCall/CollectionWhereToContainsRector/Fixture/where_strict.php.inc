<?php

namespace RectorLaravel\Tests\Rector\MethodCall\CollectionWhereToContainsRector\Fixture;

use Illuminate\Support\Collection;

$collection = new Collection([['status' => 'open']]);
$collection->whereStrict('status', 'open')->isNotEmpty();
$collection->whereStrict('status', 'open')->isEmpty();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\CollectionWhereToContainsRector\Fixture;

use Illuminate\Support\Collection;

$collection = new Collection([['status' => 'open']]);
$collection->contains('status', '===', 'open');
$collection->doesntContain('status', '===', 'open');

?>
