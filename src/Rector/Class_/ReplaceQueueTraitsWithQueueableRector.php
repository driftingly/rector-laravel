<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use Rector\PhpParser\Node\BetterNodeFinder;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\ReplaceQueueTraitsWithQueueableRector\ReplaceQueueTraitsWithQueueableRectorTest
 */
final class ReplaceQueueTraitsWithQueueableRector extends AbstractRector
{
    /**
     * @readonly
     */
    private BetterNodeFinder $betterNodeFinder;
    /**
     * @var string
     */
    private const DISPATCHABLE_TRAIT = 'Illuminate\Foundation\Bus\Dispatchable';

    /**
     * @var string
     */
    private const INTERACTS_WITH_QUEUE_TRAIT = 'Illuminate\Queue\InteractsWithQueue';

    /**
     * @var string
     */
    private const QUEUEABLE_BY_BUS_TRAIT = 'Illuminate\Bus\Queueable';

    /**
     * @var string
     */
    private const SERIALIZES_MODELS_TRAIT = 'Illuminate\Queue\SerializesModels';

    /**
     * @var string
     */
    private const QUEUEABLE_TRAIT = 'Illuminate\Foundation\Queue\Queueable';

    /**
     * @var mixed[]
     */
    private const TRAITS_TO_REPLACE = [
        self::DISPATCHABLE_TRAIT,
        self::INTERACTS_WITH_QUEUE_TRAIT,
        self::QUEUEABLE_BY_BUS_TRAIT,
        self::SERIALIZES_MODELS_TRAIT,
    ];

    public function __construct(BetterNodeFinder $betterNodeFinder)
    {
        $this->betterNodeFinder = $betterNodeFinder;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace Dispatchable, InteractsWithQueue, Queueable, and SerializesModels traits with the Queueable trait',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SomeJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SomeJob
{
    use \Illuminate\Foundation\Queue\Queueable;
}
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Node
    {
        $traitUses = $this->betterNodeFinder->findInstanceOf($node, TraitUse::class);

        if ($traitUses === []) {
            return null;
        }

        if (! $this->hasAllQueueTraits($traitUses)) {
            return null;
        }

        return $this->replaceTraitsInClass($node, $traitUses);
    }

    /**
     * @param  TraitUse[]  $traitUses
     */
    private function hasAllQueueTraits(array $traitUses): bool
    {
        $foundTraits = [];

        foreach ($traitUses as $traitUse) {
            foreach ($traitUse->traits as $trait) {
                if ($this->isQueueTrait($trait)) {
                    $foundTraits[$this->getName($trait)] = true;
                }
            }
        }

        return count($foundTraits) === count(self::TRAITS_TO_REPLACE);
    }

    /**
     * @param  TraitUse[]  $traitUses
     */
    private function replaceTraitsInClass(Class_ $class, array $traitUses): Class_
    {
        $replacedFirst = false;

        foreach ($traitUses as $traitUse) {
            $newTraits = [];

            foreach ($traitUse->traits as $trait) {
                if ($this->isQueueTrait($trait)) {
                    if (! $replacedFirst) {
                        $newTraits[] = new FullyQualified(self::QUEUEABLE_TRAIT);
                        $replacedFirst = true;
                    }
                } else {
                    $newTraits[] = $trait;
                }
            }

            if ($newTraits === []) {
                unset($class->stmts[array_search($traitUse, $class->stmts, true)]);
                $class->stmts = array_values($class->stmts);
            } else {
                $traitUse->traits = $newTraits;
            }
        }

        return $class;
    }

    private function isQueueTrait(Name $name): bool
    {
        foreach (self::TRAITS_TO_REPLACE as $traitToReplace) {
            if ($this->isName($name, $traitToReplace)) {
                return true;
            }
        }

        return false;
    }
}
