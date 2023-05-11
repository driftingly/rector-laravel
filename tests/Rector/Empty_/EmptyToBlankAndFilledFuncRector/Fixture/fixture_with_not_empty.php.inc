<?php

namespace RectorLaravel\Tests\Rector\Empty_\EmptyToBlankAndFilledFuncRector\Fixture;

!empty('');
!empty('   ');
!empty(null);
!empty(true);
!empty([]);
!empty(0);
!empty(true);
!empty(false);
!empty(collect());

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Empty_\EmptyToBlankAndFilledFuncRector\Fixture;

filled('');
filled('   ');
filled(null);
filled(true);
filled([]);
filled(0);
filled(true);
filled(false);
filled(collect());

?>
