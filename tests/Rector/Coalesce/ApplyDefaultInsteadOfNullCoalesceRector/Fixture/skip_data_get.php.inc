<?php

namespace RectorLaravel\Tests\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector\Fixture;

data_get(['foo' => null], 'foo') ?? 'bar';
data_get([], 'key') ?? false;
