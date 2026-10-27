<?php

namespace RectorLaravel\Tests\Rector\FuncCall\ArgumentFuncCallToMethodCallRector\Source;

class ParentWithFinalConstructor
{
    final public function __construct(?string $name) {}
}
