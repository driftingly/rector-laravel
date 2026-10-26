<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticFacadeToServiceMethodCallRector\Source;

class ParentWithFinalConstructor
{
    final public function __construct(?string $name) {}
}
