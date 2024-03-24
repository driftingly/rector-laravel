<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RefactorBlueprintGeometryColumnsRector\Fixture;

/** @var \Illuminate\Database\Schema\Blueprint $table */
$table->point('coordinates')->spatialIndex();
$table->linestring('coordinates')->spatialIndex();
$table->polygon('coordinates')->spatialIndex();
$table->geometrycollection('coordinates')->spatialIndex();
$table->multipoint('coordinates')->spatialIndex();
$table->multilinestring('coordinates')->spatialIndex();
$table->multipolygon('coordinates')->spatialIndex();

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RefactorBlueprintGeometryColumnsRector\Fixture;

/** @var \Illuminate\Database\Schema\Blueprint $table */
$table->geometry('coordinates', 'point')->spatialIndex();
$table->geometry('coordinates', 'linestring')->spatialIndex();
$table->geometry('coordinates', 'polygon')->spatialIndex();
$table->geometry('coordinates', 'geometrycollection')->spatialIndex();
$table->geometry('coordinates', 'multipoint')->spatialIndex();
$table->geometry('coordinates', 'multilinestring')->spatialIndex();
$table->geometry('coordinates', 'multipolygon')->spatialIndex();

?>
