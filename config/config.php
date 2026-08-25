<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\NodeAnalyzer\ApplicationAnalyzer;
use RectorLaravel\NodeVisitor\ArrayDimFetchContextNodeVisitor;
use RectorLaravel\NodeVisitor\RandomEnumContextNodeVisitor;

/**
 * to be imported, don't use RectorConfigBuilder for safe usage
 */
return static function (RectorConfig $rectorConfig): void {
    // shared single instance, so version set in tests reaches the rules that inject it
    $rectorConfig->singleton(ApplicationAnalyzer::class);

    $rectorConfig->singleton(ArrayDimFetchContextNodeVisitor::class);
    $rectorConfig->singleton(RandomEnumContextNodeVisitor::class);
};
