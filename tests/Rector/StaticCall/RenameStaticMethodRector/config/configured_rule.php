<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\StaticCall\RenameStaticMethodRector;
use RectorLaravel\Tests\Rector\StaticCall\RenameStaticMethodRector\Source\NewClass;
use RectorLaravel\Tests\Rector\StaticCall\RenameStaticMethodRector\Source\OldClass;
use RectorLaravel\ValueObject\RenameStaticMethod;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');
    $rectorConfig->ruleWithConfiguration(RenameStaticMethodRector::class, [
        new RenameStaticMethod(OldClass::class, 'get', NewClass::class, 'input'),
        new RenameStaticMethod(OldClass::class, 'oldMethod', OldClass::class, 'newMethod'),
    ]);
};
