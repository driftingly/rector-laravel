<?php

namespace RectorLaravel\Tests\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected static string $factory = \RectorLaravel\Tests\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector\Source\ProductFactory::class;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    /**
     * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\RectorLaravel\Tests\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector\Source\ProductFactory>
     */
    use HasFactory;

    protected static string $factory = \RectorLaravel\Tests\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector\Source\ProductFactory::class;
}

?>
