<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

use Rector\Core\Validation\RectorAssert;
use RectorLaravel\Contract\ValueObject\ArgumentFuncCallToMethodCallInterface;

final class ArgumentFuncCallToMethodCall implements ArgumentFuncCallToMethodCallInterface
{
    public function __construct(
        private readonly string $function,
        private readonly string $class,
        private readonly ?string $methodIfArgs = null,
        private readonly ?string $methodIfNoArgs = null
    ) {
        RectorAssert::className($class);
        RectorAssert::functionName($function);
    }

    public function getFunction(): string
    {
        return $this->function;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethodIfNoArgs(): ?string
    {
        return $this->methodIfNoArgs;
    }

    public function getMethodIfArgs(): ?string
    {
        return $this->methodIfArgs;
    }
}
