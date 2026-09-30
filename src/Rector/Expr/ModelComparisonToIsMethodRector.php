<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Expr;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Expr\ModelComparisonToIsMethodRector\ModelComparisonToIsMethodRectorTest
 */
final class ModelComparisonToIsMethodRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert model ID comparisons to use the is() method',
            [new CodeSample(
                <<<'CODE_SAMPLE'
$team->user_id === $user->id;
$post->author_id === $author->id;
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
$team->user()->is($user);
$post->author()->is($author);
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Equal::class, Identical::class];
    }

    /**
     * @param  Equal|Identical  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof Equal && ! $node instanceof Identical) {
            return null;
        }

        $result = $this->matchModelComparison($node);
        if ($result === null) {
            return null;
        }

        [$leftVar, $relationshipName, $rightVar] = $result;

        if (! $this->couldBeModel($leftVar) || ! $this->couldBeModel($rightVar)) {
            return null;
        }

        $methodCall = new MethodCall($leftVar, new Identifier($relationshipName));

        return new MethodCall($methodCall, new Identifier('is'), [new Arg($rightVar)]);
    }

    /**
     * @return array{Expr, string, Expr}|null
     */
    private function matchModelComparison(Equal|Identical $node): ?array
    {
        $left = $node->left;
        $right = $node->right;

        if (! $left instanceof PropertyFetch || ! $right instanceof PropertyFetch) {
            return null;
        }

        $leftProperty = $left->name;
        $rightProperty = $right->name;

        if (! $leftProperty instanceof Identifier || ! $rightProperty instanceof Identifier) {
            return null;
        }

        $leftPropertyName = $leftProperty->name;
        $rightPropertyName = $rightProperty->name;

        if ($this->isForeignKeyToIdPattern($leftPropertyName, $rightPropertyName)) {
            // $model->foreign_key_id == $otherModel->id
            $relationshipName = $this->extractRelationshipName($leftPropertyName);

            return [$left->var, $relationshipName, $right->var];
        }

        if ($this->isForeignKeyToIdPattern($rightPropertyName, $leftPropertyName)) {
            // $otherModel->id == $model->foreign_key_id
            $relationshipName = $this->extractRelationshipName($rightPropertyName);

            return [$right->var, $relationshipName, $left->var];
        }

        return null;
    }

    private function isForeignKeyToIdPattern(string $leftProperty, string $rightProperty): bool
    {
        return str_ends_with($leftProperty, '_id') && $rightProperty === 'id';
    }

    private function extractRelationshipName(string $foreignKeyProperty): string
    {
        return substr($foreignKeyProperty, 0, -3);
    }

    private function couldBeModel(Expr $expr): bool
    {
        $objectType = new ObjectType('Illuminate\Database\Eloquent\Model');

        if ($expr instanceof PropertyFetch) {
            $varType = $this->getType($expr->var);
            if ($this->isObjectType($expr->var, $objectType)) {
                return true;
            }

            return $varType instanceof MixedType;
        }

        if ($expr instanceof Variable) {
            $varType = $this->getType($expr);
            if ($this->isObjectType($expr, $objectType)) {
                return true;
            }

            return $varType instanceof MixedType;
        }

        return false;
    }
}
