<?php

declare(strict_types=1);

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name\FullyQualified;
use Rector\Config\RectorConfig;
use RectorLaravel\Rector\MethodCall\ReplaceServiceContainerCallArgRector;
use RectorLaravel\ValueObject\ReplaceServiceContainerCallArg;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');

    $servicesMap = [
        'events' => 'Illuminate\Contracts\Events\Dispatcher',
        'config' => 'Illuminate\Contracts\Config\Repository',
        'log' => 'Psr\Log\LoggerInterface',
        'router' => 'Illuminate\Routing\Router',
        'url' => 'Illuminate\Contracts\Routing\UrlGenerator',
        'redirect' => 'Illuminate\Routing\Redirector',
        'auth' => 'Illuminate\Contracts\Auth\Factory',
        'cookie' => 'Illuminate\Cookie\CookieJar',
        'db' => 'Illuminate\Database\ConnectionResolverInterface',
        'db.connection' => 'Illuminate\Database\ConnectionInterface',
        'encrypter' => 'Illuminate\Encryption\Encrypter',
        'files' => 'Illuminate\Filesystem\Filesystem',
        'filesystem' => 'Illuminate\Contracts\Filesystem\Factory',
        'session' => 'Illuminate\Session\SessionManager',
        'session.store' => 'Illuminate\Contracts\Session\Session',
        'view' => 'Illuminate\Contracts\View\Factory',
        'blade.compiler' => 'Illuminate\View\Compilers\BladeCompiler',
        'cache' => 'Illuminate\Contracts\Cache\Factory',
        'cache.store' => 'Illuminate\Cache\Repository',
        'queue' => 'Illuminate\Queue\QueueManager',
        'queue.connection' => 'Illuminate\Contracts\Queue\Queue',
        'hash' => 'Illuminate\Hashing\HashManager',
        'hash.driver' => 'Illuminate\Contracts\Hashing\Hasher',
        'mail.manager' => 'Illuminate\Contracts\Mail\Factory',
        'mailer' => 'Illuminate\Mail\Mailer',
        'auth.password' => 'Illuminate\Contracts\Auth\PasswordBrokerFactory',
        'auth.password.broker' => 'Illuminate\Contracts\Auth\PasswordBroker',
        'redis' => 'Illuminate\Contracts\Redis\Factory',
        'translator' => 'Illuminate\Contracts\Translation\Translator',
        'validator' => 'Illuminate\Contracts\Validation\Factory',
    ];

    $ruleConfig = array_map(
        fn (string $service, string $interface) => new ReplaceServiceContainerCallArg(
            $service,
            new ClassConstFetch(
                new FullyQualified($interface),
                'class'
            )
        ),
        array_keys($servicesMap),
        $servicesMap,
    );

    $rectorConfig->ruleWithConfiguration(
        ReplaceServiceContainerCallArgRector::class,
        $ruleConfig
    );
};
