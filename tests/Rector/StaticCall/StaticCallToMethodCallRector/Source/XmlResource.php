<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source;

use SimpleXMLElement;

class XmlResource
{
    public $resource;

    public function __construct()
    {
        $this->resource = new SimpleXMLElement('<root/>');
    }
}
