<?php

declare(strict_types=1);

use Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector;
use Rector\Arguments\ValueObject\ArgumentAdder;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\Visibility;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Visibility\ValueObject\ChangeMethodVisibility;

# see https://laravel.com/docs/9.x/upgrade
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig
        ->ruleWithConfiguration(ArgumentAdderRector::class, [new ArgumentAdder(
            'Illuminate\Contracts\Foundation\Application',
            'storagePath',
            0,
            'path',
            ''
        ),
        ]);

    $rectorConfig
        ->ruleWithConfiguration(ArgumentAdderRector::class, [new ArgumentAdder(
            'Illuminate\Contracts\Foundation\Application',
            'langPath',
            0,
            'path',
            ''
        ),
        ]);

    $rectorConfig
        ->ruleWithConfiguration(ArgumentAdderRector::class, [new ArgumentAdder(
            'Illuminate\Database\Eloquent\Model',
            'touch',
            0,
            'attribute',
        ),
        ]);

    $rectorConfig
        ->ruleWithConfiguration(ArgumentAdderRector::class, [new ArgumentAdder(
            'Illuminate\Queue\Failed\FailedJobProviderInterface',
            'flush',
            0,
            'hours',
        ),
        ]);

    $rectorConfig
        ->ruleWithConfiguration(ArgumentAdderRector::class, [new ArgumentAdder(
            'Illuminate\Foundation\Http\FormRequest',
            'validated',
            0,
            'key',
        ),
            new ArgumentAdder('Illuminate\Foundation\Http\FormRequest', 'validated', 1, 'default',),
        ]);

    $rectorConfig
        ->ruleWithConfiguration(ChangeMethodVisibilityRector::class, [new ChangeMethodVisibility(
            'Illuminate\Contracts\Foundation\Application',
            'ignore',
            Visibility::PUBLIC
        ),
        ]);

    $rectorConfig
        ->ruleWithConfiguration(RenameMethodRector::class, [
            new MethodCallRename('Illuminate\Support\Enumerable', 'reduceWithKeys', 'reduce'),

            new MethodCallRename('Illuminate\Support\Enumerable', 'reduceMany', 'reduceSpread'),

            new MethodCallRename('Illuminate\Mail\Message', 'getSwiftMessage', 'getSymfonyMessage'),

            new MethodCallRename('Illuminate\Mail\Mailable', 'withSwiftMessage', 'withSymfonyMessage'),

            new MethodCallRename(
                'Illuminate\Notifications\Messages\MailMessage',
                'withSwiftMessage',
                'withSymfonyMessage'
            ),

            new MethodCallRename('Illuminate\Mail\Mailer', 'getSwiftMailer', 'getSymfonyTransport'),

            new MethodCallRename('Illuminate\Mail\Mailer', 'setSwiftMailer', 'setSymfonyTransport'),

            new MethodCallRename('Illuminate\Mail\MailManager', 'createTransport', 'createSymfonyTransport'),

            new MethodCallRename('Illuminate\Testing\TestResponse', 'assertDeleted', 'assertModelMissing'),
        ]);
};
