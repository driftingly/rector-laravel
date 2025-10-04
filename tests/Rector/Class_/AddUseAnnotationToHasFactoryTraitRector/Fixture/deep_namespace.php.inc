<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;
}

?>
-----
<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    /**
     * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\RectorLaravel\Tests\Rector\Class_\AddUseAnnotationToHasFactoryTraitRector\Source\Tenant\UserFactory>
     */
    use HasFactory;
}

?>
