<?php

namespace RectorLaravel\ValueObject;

use PhpParser\Node\Expr\ClassConstFetch;

final readonly class ReplaceServiceContainerCallArg
{
    public function __construct(
        private string|ClassConstFetch $oldService,
        private string|ClassConstFetch $newService
    ) {
    }

    public function getOldService(): string|ClassConstFetch
    {
        return $this->oldService;
    }

    public function getNewService(): string|ClassConstFetch
    {
        return $this->newService;
    }
}
