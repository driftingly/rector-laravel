<?php

namespace RectorLaravel\Tests\Rector\Class_\RemoveDownMethodFromMigrationsRector\Fixture;

class NotAMigrationClass
{
    public function down(): void
    {
        // not a migration
    }
}

?>
