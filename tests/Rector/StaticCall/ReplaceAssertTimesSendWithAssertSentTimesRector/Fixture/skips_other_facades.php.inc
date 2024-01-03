<?php

namespace RectorLaravel\Tests\Rector\StaticCall\ReplaceAssertTimesSendWithAssertSentTimesRector\Fixture;

use Some\Other\Notification;

Notification::assertTimesSent(1, SomeNotification::class);
