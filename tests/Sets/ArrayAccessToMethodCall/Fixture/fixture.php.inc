<?php

namespace RectorLaravel\Tests\Sets\ArrayAccessToMethodCall;

$app = new \Illuminate\Foundation\Application();

$service = $app[\Illuminate\Contracts\Events\Dispatcher::class];

$config = new \Illuminate\Config\Repository();

$name = $config['app.name'];

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\ArrayAccessToMethodCall;

$app = new \Illuminate\Foundation\Application();

$service = $app->make(\Illuminate\Contracts\Events\Dispatcher::class);

$config = new \Illuminate\Config\Repository();

$name = $config->get('app.name');

?>
