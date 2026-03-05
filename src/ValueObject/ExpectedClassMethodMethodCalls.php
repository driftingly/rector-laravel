<?php

namespace RectorLaravel\ValueObject;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use Webmozart\Assert\Assert;

final class ExpectedClassMethodMethodCalls
{
    /**
     * @var MethodCall[]
     * @readonly
     */
    private array $expectedMethodCalls = [];
    /**
     * @var list<String_|ClassConstFetch>
     * @readonly
     */
    private array $expectedItems = [];
    /**
     * @var MethodCall[]
     * @readonly
     */
    private array $notExpectedMethodCalls = [];
    /**
     * @var list<String_|ClassConstFetch>
     * @readonly
     */
    private array $notExpectedItems = [];
    /**
     * @param  MethodCall[]  $expectedMethodCalls
     * @param  list<String_|ClassConstFetch>  $expectedItems
     * @param  MethodCall[]  $notExpectedMethodCalls
     * @param  list<String_|ClassConstFetch>  $notExpectedItems
     */
    public function __construct(
        array $expectedMethodCalls = [],
        array $expectedItems = [],
        array $notExpectedMethodCalls = [],
        array $notExpectedItems = []
    ) {
        $this->expectedMethodCalls = $expectedMethodCalls;
        $this->expectedItems = $expectedItems;
        $this->notExpectedMethodCalls = $notExpectedMethodCalls;
        $this->notExpectedItems = $notExpectedItems;
        Assert::allIsInstanceOf($this->expectedMethodCalls, MethodCall::class);
        Assert::allIsInstanceOf($this->notExpectedMethodCalls, MethodCall::class);
        Assert::allIsInstanceOfAny($this->expectedItems, [String_::class, ClassConstFetch::class]);
        Assert::allIsInstanceOfAny($this->expectedItems, [String_::class, ClassConstFetch::class]);
    }

    public function isActionable(): bool
    {
        return ! ($this->expectedMethodCalls === [] && $this->notExpectedMethodCalls === []);
    }

    /**
     * @return MethodCall[]
     */
    public function getAllMethodCalls(): array
    {
        return array_merge($this->expectedMethodCalls, $this->notExpectedMethodCalls);
    }

    /**
     * @return MethodCall[]
     */
    public function getNotExpectedMethodCalls(): array
    {
        return $this->notExpectedMethodCalls;
    }

    /**
     * @return array<int<0, max>, ClassConstFetch|String_>
     */
    public function getItemsToFake(): array
    {
        return array_unique(array_merge($this->expectedItems, $this->notExpectedItems), SORT_REGULAR);
    }

    /**
     * @return array<int<0, max>, ClassConstFetch|String_>
     */
    public function getExpectedItems(): array
    {
        return array_unique($this->expectedItems, SORT_REGULAR);
    }

    /**
     * @return array<int<0, max>, ClassConstFetch|String_>
     */
    public function getNotExpectedItems(): array
    {
        return array_unique($this->notExpectedItems, SORT_REGULAR);
    }
}
