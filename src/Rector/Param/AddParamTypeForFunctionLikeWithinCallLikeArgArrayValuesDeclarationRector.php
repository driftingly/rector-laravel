<?php

namespace RectorLaravel\Rector\Param;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\NodeTypeResolver\TypeComparator\TypeComparator;
use Rector\Php\PhpVersionProvider;
use Rector\PHPStanStaticTypeMapper\Enum\TypeKind;
use Rector\Rector\AbstractRector;
use Rector\StaticTypeMapper\StaticTypeMapper;
use Rector\TypeDeclaration\ValueObject\AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration;
use Rector\ValueObject\PhpVersionFeature;
use RectorPrefix202402\Webmozart\Assert\Assert;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class AddParamTypeForFunctionLikeWithinCallLikeArgArrayValuesDeclarationRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @readonly
     * @var \Rector\NodeTypeResolver\TypeComparator\TypeComparator
     */
    private $typeComparator;
    /**
     * @readonly
     * @var \Rector\Php\PhpVersionProvider
     */
    private $phpVersionProvider;
    /**
     * @readonly
     * @var \Rector\StaticTypeMapper\StaticTypeMapper
     */
    private $staticTypeMapper;
    /**
     * @var AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration[]
     */
    private $addParamTypeForFunctionLikeParamDeclarations = [];
    /**
     * @var bool
     */
    private $hasChanged = \false;
    public function __construct(TypeComparator $typeComparator, PhpVersionProvider $phpVersionProvider, StaticTypeMapper $staticTypeMapper)
    {
        $this->typeComparator = $typeComparator;
        $this->phpVersionProvider = $phpVersionProvider;
        $this->staticTypeMapper = $staticTypeMapper;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add param type for function like within call like arg array values', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
new \SomeNamespace\SomeClass::method(['value' => function ($value) {
    return $value;
}]);
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
new \SomeNamespace\SomeClass::method(['value' => function (int $value) {
    return $value;
}]);
CODE_SAMPLE,
                [
                    new AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration(
                        'SomeNamespace\SomeClass',
                        'method',
                        0,
                        0,
                        new IntegerType(),
                    )
                ]
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Node\Expr\MethodCall::class, Node\Expr\StaticCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        $this->hasChanged = \false;
        foreach ($this->addParamTypeForFunctionLikeParamDeclarations as $addParamTypeForFunctionLikeParamDeclaration) {
            $type = match (true) {
                $node instanceof MethodCall => $node->var,
                $node instanceof StaticCall => $node->class,
                default => null,
            };
            if ($type === null) {
                continue;
            }
            if (!$this->isObjectType($type, $addParamTypeForFunctionLikeParamDeclaration->getObjectType())) {
                continue;
            }
            if (($node->name ?? null) === null) {
                continue;
            }
            if (!$node->name instanceof Identifier) {
                continue;
            }
            if (!$this->isName($node->name, $addParamTypeForFunctionLikeParamDeclaration->getMethodName())) {
                continue;
            }
            $this->processFunctionLike($node, $addParamTypeForFunctionLikeParamDeclaration);
        }
        if (!$this->hasChanged) {
            return null;
        }
        return $node;
    }

    private function processFunctionLike(CallLike $callLike, AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration $addParamTypeForFunctionLikeWithinCallLikeArgDeclaration) : void
    {
        if ($callLike->isFirstClassCallable()) {
            return;
        }
        if (\is_int($addParamTypeForFunctionLikeWithinCallLikeArgDeclaration->getCallLikePosition())) {
            if ($callLike->getArgs() === []) {
                return;
            }
            $arg = $callLike->args[$addParamTypeForFunctionLikeWithinCallLikeArgDeclaration->getCallLikePosition()] ?? null;
            if (!$arg instanceof Arg) {
                return;
            }
            // int positions shouldn't have names
            if ($arg->name !== null) {
                return;
            }
        } else {
            $args = \array_filter($callLike->getArgs(), static function (Arg $arg) use ($addParamTypeForFunctionLikeWithinCallLikeArgDeclaration): bool {
                if ($arg->name === null) {
                    return \false;
                }
                return $arg->name->name === $addParamTypeForFunctionLikeWithinCallLikeArgDeclaration->getCallLikePosition();
            });
            if ($args === []) {
                return;
            }
            $arg = \array_values($args)[0];
        }
        $array = $arg->value;
        if (!$array instanceof Node\Expr\Array_) {
            return;
        }
        foreach ($array->items as $item) {
            if ($item === null) {
                continue;
            }
            if ($item->value === null) {
                continue;
            }
            if (!$item->value instanceof FunctionLike) {
                continue;
            }
            $functionLike = $item->value;
            if (!isset($functionLike->params[$addParamTypeForFunctionLikeWithinCallLikeArgDeclaration->getFunctionLikePosition()])) {
                return;
            }
            $this->refactorParameter($functionLike->params[$addParamTypeForFunctionLikeWithinCallLikeArgDeclaration->getFunctionLikePosition()], $addParamTypeForFunctionLikeWithinCallLikeArgDeclaration);
        }
    }
    private function refactorParameter(Param $param, AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration $addParamTypeForFunctionLikeWithinCallLikeArgDeclaration) : void
    {
        // already set → no change
        if ($param->type !== null) {
            $currentParamType = $this->staticTypeMapper->mapPhpParserNodePHPStanType($param->type);
            if ($this->typeComparator->areTypesEqual($currentParamType, $addParamTypeForFunctionLikeWithinCallLikeArgDeclaration->getParamType())) {
                return;
            }
        }
        $paramTypeNode = $this->staticTypeMapper->mapPHPStanTypeToPhpParserNode($addParamTypeForFunctionLikeWithinCallLikeArgDeclaration->getParamType(), TypeKind::PARAM);
        $this->hasChanged = \true;
        // remove it
        if ($addParamTypeForFunctionLikeWithinCallLikeArgDeclaration->getParamType() instanceof MixedType) {
            if ($this->phpVersionProvider->isAtLeastPhpVersion(PhpVersionFeature::MIXED_TYPE)) {
                $param->type = $paramTypeNode;
                return;
            }
            $param->type = null;
            return;
        }
        $param->type = $paramTypeNode;
    }

    public function configure(array $configuration) : void
    {
        Assert::allIsAOf($configuration, AddParamTypeForFunctionLikeWithinCallLikeArgDeclaration::class);
        $this->addParamTypeForFunctionLikeParamDeclarations = $configuration;
    }
}
