<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\UnionType;
use Rector\PHPStanStaticTypeMapper\Enum\TypeKind;
use Rector\StaticTypeMapper\StaticTypeMapper;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\FilledAndBlankToTypedComparisonRector\FilledAndBlankToTypedComparisonRectorTest
 */
final class FilledAndBlankToTypedComparisonRector extends AbstractRector
{
    public function __construct(
        private readonly StaticTypeMapper $staticTypeMapper,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replaces filled() and blank() with typed comparison.',
            [new CodeSample(
                <<<'CODE_SAMPLE'
filled($array);
blank($array);
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
$array !== [];
$array === [];
CODE_SAMPLE
            ), new CodeSample(
                <<<'CODE_SAMPLE'
filled($string);
blank($string);
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
$string !== '';
$string === '';
CODE_SAMPLE
            ), new CodeSample(
                <<<'CODE_SAMPLE'
filled($nullableClass);
blank($nullableClass);
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
$nullableClass instanceOf ClassName;
! $nullableClass instanceof ClassName;
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param  FuncCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isNames($node, ['filled', 'blank'])) {
            return null;
        }

        if (! $node->args[0] instanceof Arg) {
            return null;
        }

        $argumentType = $this->getType($node->args[0]->value);

        if ($argumentType->isString()->yes()) {
            if ($this->isName($node, 'filled')) {
                return new NotIdentical(
                    $node->args[0]->value,
                    new String_('')
                );
            } elseif ($this->isName($node, 'blank')) {
                return new Identical(
                    $node->args[0]->value,
                    new String_('')
                );
            }
        } elseif ($argumentType->isArray()->yes()) {
            if ($this->isName($node, 'filled')) {
                return new NotIdentical(
                    $node->args[0]->value,
                    new Array_
                );
            } elseif ($this->isName($node, 'blank')) {
                return new Identical(
                    $node->args[0]->value,
                    new Array_
                );
            }
        } elseif ($argumentType instanceof UnionType) {
            $type = $this->staticTypeMapper->mapPHPStanTypeToPhpParserNode($argumentType, TypeKind::PARAM);
            if (! $type instanceof NullableType) {
                return null;
            }
            if (! $type->type instanceof Name) {
                return null;
            }
            if ($this->isName($node, 'filled')) {
                return new Instanceof_(
                    $node->args[0]->value,
                    $type->type,
                );
            } elseif ($this->isName($node, 'blank')) {
                return new BooleanNot(new Instanceof_(
                    $node->args[0]->value,
                    $type->type,
                ));
            }
        }

        return null;
    }
}
