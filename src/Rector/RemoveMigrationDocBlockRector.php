<?php

declare(strict_types=1);

namespace RectorLaravel\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use RectorLaravel\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class RemoveMigrationDocBlockRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove standard Laravel migration docblocks',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
/**
 * Run the migrations.
 *
 * @return void
 */
public function up()
{
    // ...
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
public function up()
{
    // ...
}
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        $docComment = $node->getDocComment();
        if ($docComment === null) {
            return null;
        }

        // Additional safety: only process up() and down() methods in migrations
        $methodName = $node->name->toString();
        if (!in_array($methodName, ['up', 'down'])) {
            return null;
        }

        // Check for standard migration docblocks
        $patterns = [
            '/\/\*\*\s*\n\s*\*\s*Run the migrations\.\s*(\n\s*\*\s*\n\s*\*\s*@return void\s*)?\n\s*\*\//',
            '/\/\*\*\s*\n\s*\*\s*Reverse the migrations\.\s*(\n\s*\*\s*\n\s*\*\s*@return void\s*)?\n\s*\*\//'
        ];

        $docText = $docComment->getText();
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $docText)) {
                $node->setAttribute('comments', []);

                return $node;
            }
        }

        return null;
    }
}
