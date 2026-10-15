<?php

namespace RectorLaravel\Tests\Rector\MethodCall\CollectionWhereToContainsRector\Fixture;

use Illuminate\Support\Collection;

$collection = new Collection([['status' => 'open'], ['status' => 'closed']]);
$collection->where('status', 'open')->isNotEmpty();
$collection->where('status', 'open')->isEmpty();
$collection->where('priority', '>', 3)->isNotEmpty();
$collection->where('priority', '>', 3)->isEmpty();
! $collection->where('status', 'open')->isEmpty();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\CollectionWhereToContainsRector\Fixture;

use Illuminate\Support\Collection;

$collection = new Collection([['status' => 'open'], ['status' => 'closed']]);
$collection->contains('status', 'open');
$collection->doesntContain('status', 'open');
$collection->contains('priority', '>', 3);
$collection->doesntContain('priority', '>', 3);
! $collection->doesntContain('status', 'open');

?>
