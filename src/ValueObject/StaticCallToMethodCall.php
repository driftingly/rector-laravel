<?php

declare(strict_types=1);

namespace RectorLaravel\ValueObject;

use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Type\ObjectType;
use Rector\Validation\RectorAssert;

final class StaticCallToMethodCall
{
    /**
     * @readonly
     */
    private string $staticClass;
    /**
     * @readonly
     */
    private string $staticMethod;
    /**
     * @readonly
     */
    private string $classType;
    /**
     * @readonly
     */
    private string $methodName;
    public function __construct(
        string $staticClass,
        string $staticMethod,
        string $classType,
        string $methodName
    ) {
        $this->staticClass = $staticClass;
        $this->staticMethod = $staticMethod;
        $this->classType = $classType;
        $this->methodName = $methodName;
        RectorAssert::className($staticClass);
        // special char to match all method names
        if ($staticMethod !== '*') {
            RectorAssert::methodName($staticMethod);
        }

        RectorAssert::className($classType);
        if ($methodName !== '*') {
            RectorAssert::methodName($methodName);
        }
    }

    public function getClassObjectType(): ObjectType
    {
        return new ObjectType($this->classType);
    }

    public function getClassType(): string
    {
        return $this->classType;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function isStaticCallMatch(StaticCall $staticCall): bool
    {
        if (! $staticCall->class instanceof Name) {
            return false;
        }

        if ($staticCall->class->toString() !== $this->staticClass) {
            return false;
        }

        if (! $staticCall->name instanceof Identifier) {
            return false;
        }

        // all methods
        if ($this->staticMethod === '*') {
            return true;
        }

        return $staticCall->name->toString() === $this->staticMethod;
    }
}
