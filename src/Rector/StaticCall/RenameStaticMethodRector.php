<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\StaticCall;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\StaticCall\RenameStaticMethodRector\RenameStaticMethodRectorTest;
use RectorLaravel\ValueObject\RenameStaticMethod;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see RenameStaticMethodRectorTest
 */
final class RenameStaticMethodRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array<string, RenameStaticMethod[]>
     */
    private array $renameStaticMethodsByOldMethod = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Turn static method names to new ones, optionally on a new class', [
            new ConfiguredCodeSample(
                '\Illuminate\Support\Facades\Input::get("name");',
                '\Illuminate\Support\Facades\Request::input("name");',
                [new RenameStaticMethod('Illuminate\Support\Facades\Input', 'get', 'Illuminate\Support\Facades\Request', 'input')]
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param  StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        $methodName = $this->getName($node->name);
        if ($methodName === null) {
            return null;
        }

        foreach ($this->renameStaticMethodsByOldMethod[strtolower($methodName)] ?? [] as $renameStaticMethod) {
            if (! $this->isObjectType($node->class, $renameStaticMethod->getOldObjectType())) {
                continue;
            }

            $node->name = new Identifier($renameStaticMethod->getNewMethod());

            if ($renameStaticMethod->hasClassChanged()) {
                $node->class = new FullyQualified($renameStaticMethod->getNewClass());
            }

            return $node;
        }

        return null;
    }

    /**
     * @param  mixed[]  $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allIsAOf($configuration, RenameStaticMethod::class);

        // bucketed by lowercased old method name, as method names are case-insensitive
        $this->renameStaticMethodsByOldMethod = [];
        foreach ($configuration as $renameStaticMethod) {
            $this->renameStaticMethodsByOldMethod[strtolower($renameStaticMethod->getOldMethod())][] = $renameStaticMethod;
        }
    }
}
