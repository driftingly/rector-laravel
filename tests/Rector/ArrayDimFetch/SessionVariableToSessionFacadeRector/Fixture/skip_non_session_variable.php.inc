<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\SessionVariableToSessionFacadeRector\Fixture;

$_SESSIONA['VARIABLE'];
$_SESSIONA['VARIABLE'] = 'value';
unset($_SESSIONA['VARIABLE']);
isset($_SESSIONA['VARIABLE']);

?>
