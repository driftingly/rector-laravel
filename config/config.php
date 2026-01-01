<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Contract\PhpParser\DecoratingNodeVisitorInterface;
use RectorLaravel\NodeVisitor\ArrayDimFetchContextNodeVisitor;
use RectorLaravel\NodeVisitor\RandomEnumContextNodeVisitor;

/**
 * to be imported, don't use RectorConfigBuilder for safe usage
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(ArrayDimFetchContextNodeVisitor::class);
    $rectorConfig->tag(ArrayDimFetchContextNodeVisitor::class, DecoratingNodeVisitorInterface::class);

    $rectorConfig->singleton(RandomEnumContextNodeVisitor::class);
    $rectorConfig->tag(RandomEnumContextNodeVisitor::class, DecoratingNodeVisitorInterface::class);
};
