<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\UseComponentPropertyWithinCommandsRector\UseComponentPropertyWithinCommandsRectorTest
 */
class UseComponentPropertyWithinCommandsRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Use $this->components property within commands', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Illuminate\Console\Command;

class CommandWithComponents extends Command
{
    public function handle()
    {
        $this->ask('What is your name?');
        $this->line('A line!');
        $this->info('Info!');
        $this->error('Error!');
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Illuminate\Console\Command;

class CommandWithComponents extends Command
{
    public function handle()
    {
        $this->components->ask('What is your name?');
        $this->components->line('A line!');
        $this->components->info('Info!');
        $this->components->error('Error!');
    }
}
CODE_SAMPLE
                ,
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param  Class_  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->extends instanceof Name) {
            return null;
        }

        if (! $this->isObjectType($node->extends, new ObjectType('Illuminate\Console\Command'))) {
            return null;
        }

        $hasChanged = false;

        foreach ($node->stmts as $key => $stmt) {
            if (! $stmt instanceof ClassMethod) {
                continue;
            }

            $changedClassMethod = $this->refactorClassMethod($stmt);

            if ($changedClassMethod instanceof ClassMethod) {
                $node->stmts[$key] = $changedClassMethod;

                $hasChanged = true;
            }
        }

        return $hasChanged
            ? $node
            : null;

    }

    private function refactorClassMethod(ClassMethod $classMethod): ?ClassMethod
    {
        if ($classMethod->stmts === null) {
            return null;
        }

        $hasChanged = false;

        foreach ($classMethod->stmts as $stmt) {
            if (! $stmt instanceof Expression) {
                continue;
            }
            if (! $stmt->expr instanceof MethodCall) {
                continue;
            }

            if (! $this->isName($stmt->expr->var, 'this')) {
                continue;
            }

            if (! $this->isNames($stmt->expr->name, [
                'ask',
                'line',
                'info',
                'error',
                'warn',
                'confirm',
                'askWithCompletion',
                'choice',
                'alert',
            ])) {
                continue;
            }

            $stmt->expr->var =
                new PropertyFetch(new Variable('this'), 'components');

            $hasChanged = true;
        }

        return $hasChanged
            ? $classMethod
            : null;
    }
}
