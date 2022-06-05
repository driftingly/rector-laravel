<?php

require __DIR__ . '/../vendor/autoload.php';

// autoload rector first, but with local paths
// build preload file to autoload local php-parser instead of phpstan one, e.g. in case of early upgrade
exec('php vendor/rector/rector-src/build/build-preload.php .');
sleep(3);

require __DIR__ . '/../preload.php';

unlink(__DIR__ . '/../preload.php');
