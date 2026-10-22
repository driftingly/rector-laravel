<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source;

abstract class ClassWithFileSystem
{
    /**
     * @var TargetFileSystem
     */
    public $smartFileSystemProperty;
}
