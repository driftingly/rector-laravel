<?php

namespace RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Fixture;

$closure = function() {
    return 'please queue me!';
};
$closureArrow = fn() => 'please queue me!';

dispatch($closure);
dispatch($closureArrow);
dispatch(fn () => 'please queue me!');
dispatch(function () {
    return 'please queue me!';
});

\Illuminate\Support\Facades\Bus::dispatch($closure);
\Illuminate\Support\Facades\Bus::dispatch($closureArrow);
\Illuminate\Support\Facades\Bus::dispatch(fn () => 'please queue me!');
\Illuminate\Support\Facades\Bus::dispatch(function () {
   return 'please queue me!';
});

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Fixture;

$closure = function() {
    return 'please queue me!';
};
$closureArrow = fn() => 'please queue me!';

dispatch($closure);
dispatch($closureArrow);
dispatch(fn () => 'please queue me!');
dispatch(function () {
    return 'please queue me!';
});

\Illuminate\Support\Facades\Bus::dispatchSync($closure);
\Illuminate\Support\Facades\Bus::dispatchSync($closureArrow);
\Illuminate\Support\Facades\Bus::dispatchSync(fn () => 'please queue me!');
\Illuminate\Support\Facades\Bus::dispatchSync(function () {
   return 'please queue me!';
});

?>