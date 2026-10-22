<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Fixture;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source\ResourceWithFinalConstruct;

class SkipWhenParentHasFinalConstruct extends ResourceWithFinalConstruct
{
    public function toArray(
        Request $request,
    ): array {
        return [
            'user_id' => $this->user_id ?? App::get(MissingValue::class),
        ];
    }
}
