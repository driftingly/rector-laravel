<?php

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Param;
use PHPStan\Type\ObjectType;
use Rector\NodeTypeResolver\TypeComparator\TypeComparator;
use Rector\PHPStanStaticTypeMapper\Enum\TypeKind;
use Rector\Rector\AbstractRector;
use Rector\StaticTypeMapper\StaticTypeMapper;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\TypeHintTappableCallRector\TypeHintTappableCallRectorTest
 */
class TypeHintTappableCallRector extends AbstractRector
{
    /**
     * @readonly
     * @var \Rector\NodeTypeResolver\TypeComparator\TypeComparator
     */
    private $typeComparator;
    /**
     * @readonly
     * @var \Rector\StaticTypeMapper\StaticTypeMapper
     */
    private $staticTypeMapper;
    private const TAPPABLE_TRAIT = 'Illuminate\Support\Traits\Tappable';

    public function __construct(TypeComparator $typeComparator, StaticTypeMapper $staticTypeMapper)
    {
        $this->typeComparator = $typeComparator;
        $this->staticTypeMapper = $staticTypeMapper;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Automatically type hints your tappable closures',
            [
                new CodeSample(<<<'CODE_SAMPLE'
tap($collection, function ($collection) {}
CODE_SAMPLE
,
                    <<<'CODE_SAMPLE'
tap($collection, function (Collection $collection) {}
CODE_SAMPLE
                ),
                new CodeSample(<<<'CODE_SAMPLE'
(new Collection)->tap(function ($collection) {}
CODE_SAMPLE
,
                    <<<'CODE_SAMPLE'
(new Collection)->tap(function (Collection $collection) {}
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class, FuncCall::class];
    }

    /**
     * @param  MethodCall|FuncCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isName($node->name, 'tap')) {
            return null;
        }

        if ($node->isFirstClassCallable()) {
            return null;
        }

        if ($node instanceof MethodCall && $node->getArgs() !== []) {
            return $this->refactorMethodCall($node);
        }

        if (count($node->getArgs()) < 2 || ! $node->getArgs()[1]->value instanceof Closure) {
            return null;
        }

        /** @var Closure $closure */
        $closure = $node->getArgs()[1]->value;

        if ($closure->getParams() === []) {
            return null;
        }

        $this->refactorParameter($closure->getParams()[0], $node->getArgs()[0]->value);

        return $node;
    }

    private function refactorParameter(Param $param, Node $node): void
    {
        $nodePhpStanType = $this->nodeTypeResolver->getType($node);

        // already set → no change
        if ($param->type instanceof Node) {
            $currentParamType = $this->staticTypeMapper->mapPhpParserNodePHPStanType($param->type);
            if ($this->typeComparator->areTypesEqual($currentParamType, $nodePhpStanType)) {
                return;
            }
        }

        $paramTypeNode = $this->staticTypeMapper->mapPHPStanTypeToPhpParserNode($nodePhpStanType, TypeKind::PARAM);
        $param->type = $paramTypeNode;
    }

    private function refactorMethodCall(MethodCall $methodCall): ?MethodCall
    {
        if (! $this->isTappableCall($methodCall)) {
            return null;
        }

        if (! $methodCall->getArgs()[0]->value instanceof Closure) {
            return null;
        }

        /** @var Closure $closure */
        $closure = $methodCall->getArgs()[0]->value;

        if ($closure->getParams() === []) {
            return null;
        }

        $this->refactorParameter($closure->getParams()[0], $methodCall->var);

        return $methodCall;
    }

    private function isTappableCall(MethodCall $methodCall): bool
    {
        return $this->isObjectType($methodCall->var, new ObjectType(self::TAPPABLE_TRAIT));
    }
}
