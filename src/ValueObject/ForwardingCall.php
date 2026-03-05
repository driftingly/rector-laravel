<?php

namespace RectorLaravel\ValueObject;

use PhpParser\Node\Expr;

final class ForwardingCall
{
    /**
     * @readonly
     */
    private Expr $object;
    /**
     * @readonly
     */
    private Expr $method;
    /**
     * @readonly
     */
    private Expr $args;
    public function __construct(Expr $object, Expr $method, Expr $args)
    {
        $this->object = $object;
        $this->method = $method;
        $this->args = $args;
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
