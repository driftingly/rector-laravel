<?php

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    $rectorConfig->import(__DIR__ . '/type-declaration/eloquent.php');
    $rectorConfig->import(__DIR__ . '/type-declaration/service-container.php');
};
