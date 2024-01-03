<?php

namespace RectorLaravel\Tests\Rector\StaticCall\ReplaceAssertTimesSendWithAssertSentTimesRector\Fixture;

use Illuminate\Support\Facades\Notification;

Notification::assertTimesSent(1, SomeNotification::class);

-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\ReplaceAssertTimesSendWithAssertSentTimesRector\Fixture;

use Illuminate\Support\Facades\Notification;

Notification::assertSentTimes(SomeNotification::class, 1);
