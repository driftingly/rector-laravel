<?php

namespace Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Fixture;

$value = 1;
optional($value)->something();
optional(null)->something();
optional(5)->something();
optional(['present' => 'here'])->missing;
optional((object) ['foo' => 'bar'])->foo;
optional((object) ['foo' => 'bar'])->bar;

?>
-----
<?php

namespace Rector\Laravel\Tests\Rector\PropertyFetch\OptionalToNullsafeOperatorRector\Fixture;

$value = 1;
$value?->something();
optional(null)->something();
optional(5)->something();
optional(['present' => 'here'])->missing;
((object) ['foo' => 'bar'])?->foo;
((object) ['foo' => 'bar'])?->bar;

?>
