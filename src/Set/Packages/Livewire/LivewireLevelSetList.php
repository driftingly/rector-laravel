<?php

declare(strict_types=1);

namespace RectorLaravel\Set\Packages\Livewire;

/**
 * @deprecated Use RectorConfig::configure()->withComposerBased(laravel: true) instead, which registers the
 *             Livewire upgrade rules for the installed livewire/livewire version automatically.
 */
final class LivewireLevelSetList
{
    public const string UP_TO_LIVEWIRE = __DIR__ . '/../../../../config/sets/packages/livewire/level/up-to-livewire-30.php';
}
