<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'attribute_table')]
class SomeModelWithTableAttributeAndTableProperty extends Model
{
    protected $table = 'property_table';
}
