<?php

namespace RectorLaravel\Tests\Sets\Laravel53;

use Illuminate\Support\Collection;

(new Collection())->lists('id');
(new \Illuminate\Database\Eloquent\Builder())->lists('id');
(new \Illuminate\Database\Query\Builder())->lists('id');

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel53;

use Illuminate\Support\Collection;

(new Collection())->pluck('id');
(new \Illuminate\Database\Eloquent\Builder())->pluck('id');
(new \Illuminate\Database\Query\Builder())->pluck('id');

?>
