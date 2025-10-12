<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Expr\Cast\Array_;
use PhpParser\Node\Expr\Cast\Bool_;
use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Expr\Cast\Int_;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\FuncCall\ConfigToTypedConfigMethodCallRector\ConfigToTypedConfigMethodCallRectorTest
 */
final class ConfigToTypedConfigMethodCallRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Refactor config() calls to use type-specific methods when the expected type is known',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$name = (string) config('app.name');
$lifetime = (int) config('session.lifetime');
$debug = (bool) config('app.debug');
$version = (float) config('app.version');
$connections = (array) config('database.connections');
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$name = config()->string('app.name');
$lifetime = config()->integer('session.lifetime');
$debug = config()->boolean('app.debug');
$version = config()->float('app.version');
$connections = config()->array('database.connections');
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Cast::class];
    }

    /**
     * @param  Cast  $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->expr instanceof FuncCall) {
            return null;
        }

        $funcCall = $node->expr;

        if (! $this->isName($funcCall->name, 'config')) {
            return null;
        }

        if (count($funcCall->args) !== 1) {
            return null;
        }

        $methodName = $this->getMethodNameForCast($node);
        if ($methodName === null) {
            return null;
        }

        $configCall = new FuncCall(new Name('config'));

        return new MethodCall($configCall, new Identifier($methodName), $funcCall->args);
    }

    private function getMethodNameForCast(Cast $cast): ?string
    {
        switch (true) {
            case $cast instanceof String_:
                return 'string';
            case $cast instanceof Int_:
                return 'integer';
            case $cast instanceof Bool_:
                return 'boolean';
            case $cast instanceof Double:
                return 'float';
            case $cast instanceof Array_:
                return 'array';
            default:
                return null;
        }
    }
}
