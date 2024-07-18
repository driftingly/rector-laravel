<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\Packages\Livewire\LivewireSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([LivewireSetList::LIVEWIRE_30]);
};
