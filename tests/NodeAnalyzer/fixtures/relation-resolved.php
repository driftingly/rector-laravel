<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Fixtures;

use Illuminate\Database\Eloquent\Relations\HasMany;
use RectorLaravel\Tests\NodeAnalyzer\Source\Foo;
use RectorLaravel\Tests\NodeAnalyzer\Source\SomeModel;

/** @var HasMany<Foo, SomeModel> $query */
$query->where('a', 'b');
