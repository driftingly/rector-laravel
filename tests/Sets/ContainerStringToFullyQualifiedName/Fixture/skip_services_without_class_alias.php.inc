<?php

namespace RectorLaravel\Tests\Sets\ContainerStringToFullyQualifiedName;

app('db.factory');
app('db.schema');
app('db.transactions');
app('view.engine.resolver');
app('memcached.connector');
app('queue.worker');
app('composer');
app('validation.presence');
app('command.tinker');

?>
