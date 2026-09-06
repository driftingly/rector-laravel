<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\SessionVariableToSessionFacadeRector\Fixture;

echo "user {$_SESSION['user']}";
echo "user $_SESSION[user]";

?>
