<?php

namespace RectorLaravel\Tests\NodeAnalyzer\Source;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Laravel applies the #[Table] attribute in the constructor, so a model which
 * cannot be constructed only reports its table when the attribute is read
 * through reflection.
 */
#[Table(name: 'unconstructable_attribute_table')]
class SomeUnconstructableModelWithTableAttribute extends Model
{
    public function __construct()
    {
        throw new RuntimeException('Cannot be constructed');
    }
}
