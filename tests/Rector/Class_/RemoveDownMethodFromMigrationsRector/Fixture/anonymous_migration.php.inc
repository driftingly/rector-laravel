<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDownMethodFromMigrationsRector\Fixture;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDownMethodFromMigrationsRector\Fixture;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
        });
    }
};

?>
