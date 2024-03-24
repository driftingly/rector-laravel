<?php

namespace RectorLaravel\Tests\Rector\MethodCall\RefactorBlueprintGeometryColumnsRector\Fixture;

/** @var \stdClass $table */
$table->point('coordinates')->spatialIndex();
$table->linestring('coordinates')->spatialIndex();
$table->polygon('coordinates')->spatialIndex();
$table->geometrycollection('coordinates')->spatialIndex();
$table->multipoint('coordinates')->spatialIndex();
$table->multilinestring('coordinates')->spatialIndex();
$table->multipolygon('coordinates')->spatialIndex();

?>
