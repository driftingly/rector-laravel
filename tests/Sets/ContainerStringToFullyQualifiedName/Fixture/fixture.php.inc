<?php

namespace RectorLaravel\Tests\Sets\ContainerStringToFullyQualifiedName;

app('events');
app('log');
app('router');
app('url');
app('view');
app('validator');
app('session');
app('db');
app('auth');
app('auth.driver');
app('cookie');
app('queue');
app('queue.connection');
app('queue.failer');
app('queue.listener');
app('queue.worker');
app('cache');
app('cache.store');
app('filesystem');
app('encrypter');
app('files');
app('hash');
app('mailer');

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\ContainerStringToFullyQualifiedName;

app(\Illuminate\Contracts\Events\Dispatcher::class);
app(\Psr\Log\LoggerInterface::class);
app(\Illuminate\Routing\Router::class);
app(\Illuminate\Contracts\Routing\UrlGenerator::class);
app(\Illuminate\Contracts\View\Factory::class);
app(\Illuminate\Contracts\Validation\Factory::class);
app(\Illuminate\Session\SessionManager::class);
app(\Illuminate\Database\ConnectionResolverInterface::class);
app(\Illuminate\Contracts\Auth\Factory::class);
app(\Illuminate\Auth\SessionGuard::class);
app(\Illuminate\Cookie\CookieJar::class);
app(\Illuminate\Queue\QueueManager::class);
app(\Illuminate\Contracts\Queue\Queue::class);
app(\Illuminate\Queue\Failed\DatabaseUuidFailedJobProvider::class);
app(\Illuminate\Queue\Listener::class);
app(\Illuminate\Queue\Worker::class);
app(\Illuminate\Contracts\Cache\Factory::class);
app(\Illuminate\Cache\Repository::class);
app(\Illuminate\Contracts\Filesystem\Factory::class);
app(\Illuminate\Encryption\Encrypter::class);
app(\Illuminate\Filesystem\Filesystem::class);
app(\Illuminate\Hashing\HashManager::class);
app(\Illuminate\Mail\Mailer::class);

?>
