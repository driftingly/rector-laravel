<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\NodeVisitor\ArrayDimFetchContextNodeVisitor;
use RectorLaravel\NodeVisitor\RandomEnumContextNodeVisitor;

/**
 * to be imported, don't use RectorConfigBuilder for safe usage
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(ArrayDimFetchContextNodeVisitor::class);
    $rectorConfig->singleton(RandomEnumContextNodeVisitor::class);
};
