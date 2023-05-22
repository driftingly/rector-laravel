<?php

namespace RectorLaravel\Tests\Rector\Empty_\EmptyToBlankAndFilledFuncRector\Fixture;

empty('');
empty('   ');
empty(null);
empty(true);
empty([]);
empty(0);
empty(true);
empty(false);
empty(collect());

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Empty_\EmptyToBlankAndFilledFuncRector\Fixture;

blank('');
blank('   ');
blank(null);
blank(true);
blank([]);
blank(0);
blank(true);
blank(false);
blank(collect());

?>
