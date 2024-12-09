<?php

namespace RectorLaravel\Tests\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector\Source;

use Illuminate\Contracts\Queue\ShouldQueue;

class QueueableJob implements ShouldQueue {}
