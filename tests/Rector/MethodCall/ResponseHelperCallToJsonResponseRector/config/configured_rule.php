<?php

declare(strict_types=1);

use Illuminate\Routing\ResponseFactory;
use Rector\Config\RectorConfig;
use RectorLaravel\Rector\MethodCall\ResponseHelperCallToJsonResponseRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(ResponseHelperCallToJsonResponseRector::class);
};

function response(): ResponseFactory
{
    return new ResponseFactory();
}
