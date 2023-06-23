<?php

declare(strict_types=1);

namespace RectorLaravel\Contract\ValueObject;

interface ArgumentFuncCallToMethodCallInterface
{
    public function getFunction(): string;
}
