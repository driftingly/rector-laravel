<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use UserLand\SomeModel;

it('reloads the model', function (): void {
    /** @var SomeModel $job */
    $job = SomeModel::find(1);

    expect($job->fresh()->status)->toBe('done');
});
-----
<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use UserLand\SomeModel;

it('reloads the model', function (): void {
    /** @var SomeModel $job */
    $job = SomeModel::find(1);

    expect($job->refresh()->status)->toBe('done');
});
