<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

#[Table(name: 'unconstructable_key_table', key: 'uuid')]
class SomeUnconstructableModelWithTableAttributeKey extends Model
{
    public function __construct()
    {
        throw new RuntimeException('Cannot be constructed');
    }
}
