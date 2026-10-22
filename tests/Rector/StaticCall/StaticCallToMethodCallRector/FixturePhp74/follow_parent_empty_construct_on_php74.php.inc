<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\FixturePhp74;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source\XmlResource;

class FollowParentEmptyConstructOnPhp74 extends XmlResource
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
use RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source\XmlResource;

class FollowParentEmptyConstructOnPhp74 extends XmlResource
{
    private \Illuminate\Foundation\Application $application;
    public function __construct(\Illuminate\Foundation\Application $application)
    {
        parent::__construct();
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
