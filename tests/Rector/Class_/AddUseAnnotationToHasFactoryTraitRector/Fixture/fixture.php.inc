<?php

namespace RectorLaravel\Tests\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector\Fixture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    /**
     * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\UserFactory>
     */
    use HasFactory;
}

?>
