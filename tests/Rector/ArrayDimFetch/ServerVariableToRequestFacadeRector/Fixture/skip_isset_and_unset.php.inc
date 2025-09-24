<?php

namespace RectorLaravel\Tests\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector\Fixture;

isset($_SERVER['APP_ENV']);
unset($_SERVER['APP_ENV']);
