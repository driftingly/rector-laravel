<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

use Rector\Validation\RectorAssert;

final readonly class FuncCallToStaticCall
{
    public function __construct(
        private string $oldFuncName,
        private string $newClassName,
        private string $newMethodName
    ) {
        RectorAssert::functionName($oldFuncName);
        RectorAssert::className($newClassName);
        RectorAssert::methodName($newMethodName);
    }

    public function getOldFuncName(): string
    {
        return $this->oldFuncName;
    }

    public function getNewClassName(): string
    {
        return $this->newClassName;
    }

    public function getNewMethodName(): string
    {
        return $this->newMethodName;
    }
}
