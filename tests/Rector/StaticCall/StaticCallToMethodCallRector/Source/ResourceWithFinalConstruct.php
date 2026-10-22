<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source;

class ResourceWithFinalConstruct
{
    public $resource;

    final public function __construct($resource)
    {
        $this->resource = $resource;
    }
}
