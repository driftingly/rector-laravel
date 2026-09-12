<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table('positional_attribute_table')]
class SomeModelWithPositionalTableAttribute extends Model {}
