<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\FixturePhp74;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source\JsonResource;

class FollowParentConstructOnPhp74 extends JsonResource
{
    public function toArray(
        Request $request,
    ): array {
        return [
            'user_id' => $this->user_id ?? App::get(MissingValue::class),
        ];
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\FixturePhp74;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source\JsonResource;

class FollowParentConstructOnPhp74 extends JsonResource
{
    private \Illuminate\Foundation\Application $application;
    public function __construct($resource, \Illuminate\Foundation\Application $application)
    {
        parent::__construct($resource);
        $this->application = $application;
    }
    public function toArray(
        Request $request,
    ): array {
        return [
            'user_id' => $this->user_id ?? $this->application->get(MissingValue::class),
        ];
    }
}

?>
