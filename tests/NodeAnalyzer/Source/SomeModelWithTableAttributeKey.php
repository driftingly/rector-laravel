<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'key_attribute_table', key: 'uuid')]
class SomeModelWithTableAttributeKey extends Model {}
