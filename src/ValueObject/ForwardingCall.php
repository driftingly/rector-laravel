<?php

namespace RectorLaravel\ValueObject;

use PhpParser\Node\Expr;

final readonly class ForwardingCall
{
    public function __construct(
        private Expr $object,
        private Expr $method,
        private Expr $args,
    )
    {
    }

    public function getObject(): Expr
    {
        return $this->object;
    }


    public function getMethod(): Expr
    {
        return $this->method;
    }

    public function getArgs(): Expr
    {
        return $this->args;
    }
}
