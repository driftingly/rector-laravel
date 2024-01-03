<?php

namespace RectorLaravel\Tests\Rector\StaticCall\ReplaceAssertTimesSendWithAssertSentTimesRector\Fixture;

use Illuminate\Support\Facades\Notification;

Notification::assertSentTimes(1, SomeNotification::class);
