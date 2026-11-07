<?php

declare(strict_types=1);

namespace RectorLaravel\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\While_;
use PhpParser\NodeVisitorAbstract;

use function spl_object_id;

/**
 * Records, in one pass, the object id of every node nested in a loop or in a closure / arrow function.
 * The loop or closure node itself is not recorded, only what it contains.
 */
final class LoopAndClosureNestingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var array<int, true>
     */
    private array $nodeIdsInsideClosures = [];

    /**
     * @var array<int, true>
     */
    private array $nodeIdsInsideLoops = [];

    private int $closureDepth = 0;

    private int $loopDepth = 0;

    public function enterNode(Node $node): null
    {
        if ($this->closureDepth > 0) {
            $this->nodeIdsInsideClosures[spl_object_id($node)] = true;
        }

        if ($this->loopDepth > 0) {
            $this->nodeIdsInsideLoops[spl_object_id($node)] = true;
        }

        if ($this->isClosure($node)) {
            $this->closureDepth++;
        }

        if ($this->isLoop($node)) {
            $this->loopDepth++;
        }

        return null;
    }

    public function leaveNode(Node $node): null
    {
        if ($this->isClosure($node)) {
            $this->closureDepth--;
        }

        if ($this->isLoop($node)) {
            $this->loopDepth--;
        }

        return null;
    }

    /**
     * @return array<int, true>
     */
    public function getNodeIdsInsideClosures(): array
    {
        return $this->nodeIdsInsideClosures;
    }

    /**
     * @return array<int, true>
     */
    public function getNodeIdsInsideLoops(): array
    {
        return $this->nodeIdsInsideLoops;
    }

    private function isClosure(Node $node): bool
    {
        return $node instanceof Closure || $node instanceof ArrowFunction;
    }

    private function isLoop(Node $node): bool
    {
        return $node instanceof For_ || $node instanceof Foreach_ || $node instanceof While_ || $node instanceof Do_;
    }
}
