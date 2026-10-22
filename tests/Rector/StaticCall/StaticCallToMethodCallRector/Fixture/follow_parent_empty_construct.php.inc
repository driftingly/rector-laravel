<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Fixture;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source\XmlResource;

class FollowParentEmptyConstruct extends XmlResource
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

namespace RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Fixture;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source\XmlResource;

class FollowParentEmptyConstruct extends XmlResource
{
    public function __construct(private \Illuminate\Foundation\Application $application)
    {
        parent::__construct();
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
