<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'inherited_attribute_table')]
abstract class SomeParentModelWithTableAttribute extends Model {}
