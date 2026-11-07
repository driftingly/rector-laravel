<?php

namespace RectorLaravel\Tests\Rector\ClassMethod\ModelFreshToRefreshRector\Fixture;

use UserLand\SomeModel;

it('reloads a missing model', function (): void {
    /** @var SomeModel $job */
    $job = SomeModel::find(1);

    expect($job->fresh())->toBeNull();
});

it('keeps the null expectation with not', function (): void {
    /** @var SomeModel $job */
    $job = SomeModel::find(1);

    expect($job->fresh())->not->toBeNull();
});

it('keeps the null literal expectation', function (): void {
    /** @var SomeModel $job */
    $job = SomeModel::find(1);

    expect($job->fresh())->toBe(null);
});
