<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDownMethodFromMigrationsRector\Fixture;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
        });
    }
}

?>
