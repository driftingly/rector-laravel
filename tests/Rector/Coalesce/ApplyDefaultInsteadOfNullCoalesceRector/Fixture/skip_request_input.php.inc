<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

(new \Illuminate\Http\Request())->input('value') ?? '';
