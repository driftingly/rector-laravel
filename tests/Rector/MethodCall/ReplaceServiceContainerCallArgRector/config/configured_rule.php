<?php

declare(strict_types=1);

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name\FullyQualified;
use Rector\Config\RectorConfig;
use RectorLaravel\Rector\MethodCall\ReplaceServiceContainerCallArgRector;
use RectorLaravel\ValueObject\ReplaceServiceContainerCallArg;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->ruleWithConfiguration(
        ReplaceServiceContainerCallArgRector::class,
        [
            new ReplaceServiceContainerCallArg(
                'encrypter',
                new ClassConstFetch(
                    new FullyQualified('Illuminate\Contracts\Encryption\Encrypter'),
                    'class'
                ),
            ),
            new ReplaceServiceContainerCallArg(
                new ClassConstFetch(
                    new FullyQualified('Illuminate\Contracts\Session\Session'),
                    'class'
                ),
                'session',
            ),
        ]
    );
};
