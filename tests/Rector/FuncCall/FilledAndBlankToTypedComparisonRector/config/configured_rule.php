<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\FuncCall\FilledAndBlankToTypedComparisonRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(FilledAndBlankToTypedComparisonRector::class);
};
