<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use Rector\PhpParser\Node\FileNode;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Class_\ReplaceQueueTraitsWithQueueableRector\ReplaceQueueTraitsWithQueueableRectorTest
 */
final class ReplaceQueueTraitsWithQueueableRector extends AbstractRector
{
    private const string DISPATCHABLE_TRAIT = 'Illuminate\Foundation\Bus\Dispatchable';

    private const string INTERACTS_WITH_QUEUE_TRAIT = 'Illuminate\Queue\InteractsWithQueue';

    private const string QUEUEABLE_BY_BUS_TRAIT = 'Illuminate\Bus\Queueable';

    private const string SERIALIZES_MODELS_TRAIT = 'Illuminate\Queue\SerializesModels';

    private const string QUEUEABLE_TRAIT = 'Illuminate\Foundation\Queue\Queueable';

    private const array TRAITS_TO_REPLACE = [
        self::DISPATCHABLE_TRAIT,
        self::INTERACTS_WITH_QUEUE_TRAIT,
        self::QUEUEABLE_BY_BUS_TRAIT,
        self::SERIALIZES_MODELS_TRAIT,
    ];

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
use Illuminate\Foundation\Queue\Queueable;

class SomeJob
{
    use Queueable;
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
        return [Namespace_::class, FileNode::class];
    }

    /**
     * @param  Namespace_|FileNode  $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof FileNode && $node->isNamespaced()) {
            return null;
        }

        $class = $this->findClassWithAllQueueTraits($node);

        if (! $class instanceof Class_) {
            return null;
        }

        $this->replaceTraitsInClass($class);
        $this->replaceUseStatements($node);

        return $node;
    }

    /**
     * @param  Namespace_|FileNode  $node
     */
    private function findClassWithAllQueueTraits(Node $node): ?Class_
    {
        foreach ($node->stmts as $stmt) {
            if (! $stmt instanceof Class_) {
                continue;
            }

            $traitUses = $stmt->getTraitUses();

            if ($traitUses === []) {
                continue;
            }

            if (count($this->findQueueTraits($traitUses)) === 4) {
                return $stmt;
            }
        }

        return null;
    }

    /**
     * @param  TraitUse[]  $traitUses
     * @return array<string, true>
     */
    private function findQueueTraits(array $traitUses): array
    {
        $foundTraits = [];

        foreach ($traitUses as $traitUse) {
            foreach ($traitUse->traits as $trait) {
                foreach (self::TRAITS_TO_REPLACE as $traitToReplace) {
                    if ($this->isName($trait, $traitToReplace)) {
                        $foundTraits[$traitToReplace] = true;
                    }
                }
            }
        }

        return $foundTraits;
    }

    private function replaceTraitsInClass(Class_ $class): void
    {
        $replacedFirst = false;

        foreach ($class->getTraitUses() as $traitUse) {
            $newTraits = [];

            foreach ($traitUse->traits as $trait) {
                if ($this->isQueueTrait($trait)) {
                    if (! $replacedFirst) {
                        $newTraits[] = new Name('Queueable');
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

    /**
     * @param  Namespace_|FileNode  $node
     */
    private function replaceUseStatements(Node $node): void
    {
        $addedNewImport = false;
        $useStmtsToRemove = [];

        foreach ($node->stmts as $key => $stmt) {
            if (! $stmt instanceof Use_ || $stmt->type !== Use_::TYPE_NORMAL) {
                continue;
            }

            $newUses = [];

            foreach ($stmt->uses as $use) {
                if (in_array($use->name->toString(), self::TRAITS_TO_REPLACE, true)) {
                    if (! $addedNewImport) {
                        $newUses[] = new UseUse(new Name(self::QUEUEABLE_TRAIT));
                        $addedNewImport = true;
                    }
                } else {
                    $newUses[] = $use;
                }
            }

            if ($newUses === []) {
                $useStmtsToRemove[] = $key;
            } else {
                $stmt->uses = $newUses;
            }
        }

        foreach (array_reverse($useStmtsToRemove) as $key) {
            unset($node->stmts[$key]);
        }

        $node->stmts = array_values($node->stmts);

        if (! $addedNewImport) {
            $this->addNewImportAtTop($node);
        }
    }

    /**
     * @param  Namespace_|FileNode  $node
     */
    private function addNewImportAtTop(Node $node): void
    {
        $newUse = new Use_([new UseUse(new Name(self::QUEUEABLE_TRAIT))]);

        foreach ($node->stmts as $key => $stmt) {
            if ($stmt instanceof Use_) {
                array_splice($node->stmts, $key, 0, [$newUse]);

                return;
            }
        }

        array_unshift($node->stmts, $newUse);
    }
}
