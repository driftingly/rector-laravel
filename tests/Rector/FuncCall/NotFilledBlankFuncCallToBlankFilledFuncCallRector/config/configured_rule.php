<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(\RectorLaravel\Rector\FuncCall\NotFilledBlankFuncCallToBlankFilledFuncCallRector::class);
};
