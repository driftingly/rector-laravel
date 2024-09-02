<?php

declare(strict_types=1);

namespace RectorLaravel\Set\Packages\Livewire;

use Rector\Set\Contract\SetListInterface;

final class LivewireLevelSetList implements SetListInterface
{
    /**
     * @var string
     */
    public const UP_TO_LIVEWIRE = __DIR__ . '/../../../../config/sets/packages/livewire/level/up-to-livewire-30.php';
}
