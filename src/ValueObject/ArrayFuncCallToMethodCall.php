<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

use Rector\Validation\RectorAssert;
use RectorLaravel\Contract\ValueObject\ArgumentFuncCallToMethodCallInterface;

final readonly class ArrayFuncCallToMethodCall implements ArgumentFuncCallToMethodCallInterface
{
    /**
     * @param  non-empty-string  $function
     * @param  non-empty-string  $class
     * @param  non-empty-string  $arrayMethod
     * @param  non-empty-string  $nonArrayMethod
     */
    public function __construct(
        private string $function,
        private string $class,
        private string $arrayMethod,
        private string $nonArrayMethod
    ) {
        RectorAssert::className($class);
        RectorAssert::functionName($function);
        RectorAssert::methodName($arrayMethod);
        RectorAssert::methodName($nonArrayMethod);
    }

    public function getFunction(): string
    {
        return $this->function;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getArrayMethod(): string
    {
        return $this->arrayMethod;
    }

    public function getNonArrayMethod(): string
    {
        return $this->nonArrayMethod;
    }
}
