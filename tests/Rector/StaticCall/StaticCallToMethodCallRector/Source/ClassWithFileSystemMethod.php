<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source;

abstract class ClassWithFileSystemMethod
{
    public function getSmartFileSystem(): TargetFileSystem
    {
        return new TargetFileSystem;
    }
}
