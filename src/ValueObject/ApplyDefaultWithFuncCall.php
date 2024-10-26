<?php

namespace RectorLaravel\ValueObject;

final readonly class ApplyDefaultWithFuncCall
{
    public function __construct(private string $functionName, private int $argumentPosition = 1)
    {
    }

    public function getFunctionName(): string
    {
        return $this->functionName;
    }

    public function getArgumentPosition(): int
    {
        return $this->argumentPosition;
    }
}
