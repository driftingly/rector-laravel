<?php

namespace RectorLaravel\Tests\Sets\Laravel53;

(new \Illuminate\Support\Collection())->lists('id');
(new \Illuminate\Database\Eloquent\Builder())->lists('id');
(new \Illuminate\Database\Query\Builder())->lists('id');
(new \Illuminate\Database\Eloquent\Collection())->withHidden([]);
(new \Illuminate\Database\Eloquent\Model())->withHidden([]);
\Illuminate\Support\Str::randomBytes(16);
\Illuminate\Support\Str::equals('knownString', 'userInput');

class SomeJob implements \Illuminate\Contracts\Bus\SelfHandling
{
}

class SomeScope implements \Illuminate\Database\Eloquent\ScopeInterface
{
}

class SomeView extends \Illuminate\View\Expression
{
}

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel53;

(new \Illuminate\Support\Collection())->pluck('id');
(new \Illuminate\Database\Eloquent\Builder())->pluck('id');
(new \Illuminate\Database\Query\Builder())->pluck('id');
(new \Illuminate\Database\Eloquent\Collection())->makeVisible([]);
(new \Illuminate\Database\Eloquent\Model())->makeVisible([]);
\random_bytes(16);
\hash_equals('knownString', 'userInput');

class SomeJob
{
}

class SomeScope implements \Illuminate\Database\Eloquent\Scope
{
}

class SomeView extends \Illuminate\Support\HtmlString
{
}

?>
