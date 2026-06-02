<?php

namespace RectorLaravel\Rector\ArrayDimFetch;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\StaticCall;
use Rector\NodeTypeResolver\Node\AttributeKey;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\ArrayDimFetch\EnvVariableToEnvHelperRector\EnvVariableToEnvHelperRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see EnvVariableToEnvHelperRectorTest
 */
class EnvVariableToEnvHelperRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change env variable to env static call',
            [new CodeSample(
                <<<'CODE_SAMPLE'
$_ENV['APP_NAME'];
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
\Illuminate\Support\Env::get('APP_NAME');
CODE_SAMPLE
            )]
        );
    }

    public function getNodeTypes(): array
    {
        return [ArrayDimFetch::class];
    }

    /**
     * @param  ArrayDimFetch  $node
     */
    public function refactor(Node $node): ?StaticCall
    {
        if ($node->getAttribute(AttributeKey::IS_BEING_ASSIGNED) === true) {
            return null;
        }

        if (! $this->isName($node->var, '_ENV')) {
            return null;
        }

        if ($node->dim === null) {
            return null;
        }

        return $this->nodeFactory->createStaticCall('Illuminate\Support\Env', 'get', [
            new Arg($node->dim),
        ]);
    }
}
