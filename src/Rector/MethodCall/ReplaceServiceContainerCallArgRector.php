<?php

namespace RectorLaravel\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use RectorLaravel\ValueObject\ReplaceServiceContainerCallArg;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \RectorLaravel\Tests\Rector\MethodCall\ReplaceServiceContainerCallArgRector\ReplaceServiceContainerCallArgRectorTest
 */
class ReplaceServiceContainerCallArgRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var ReplaceServiceContainerCallArg[]
     */
    private $replaceServiceContainerCallArgs = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Changes the string or class const used for a service container make call',
            [new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
app('encrypter')->encrypt('...');
\Illuminate\Support\Facades\Application::make('encrypter')->encrypt('...');
CODE_SAMPLE
,
                <<<'CODE_SAMPLE'
app(Illuminate\Contracts\Encryption\Encrypter::class)->encrypt('...');
\Illuminate\Support\Facades\Application::make(Illuminate\Contracts\Encryption\Encrypter::class)->encrypt('...');
CODE_SAMPLE
,
                [
                    new ReplaceServiceContainerCallArg('encrypter', new ClassConstFetch(
                        new Name('Illuminate\Contracts\Encryption\Encrypter'),
                        'class'
                    )),
                ]
            )]
        );
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class, FuncCall::class];
    }

    /**
     * @param  MethodCall|StaticCall|FuncCall  $node
     * @return \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall|\PhpParser\Node\Expr\FuncCall|null
     */
    public function refactor(Node $node)
    {
        if (! $this->validMethodCall($node) &&
            ! $this->validFuncCall($node)) {
            return null;
        }

        if ($node->args === [] || ! $node->args[0] instanceof Arg) {
            return null;
        }

        $hasChanged = false;

        foreach ($this->replaceServiceContainerCallArgs as $replaceServiceContainerCallArg) {
            if ($this->isMatchForChangeServiceContainerCallArgValue($node->args[0], $replaceServiceContainerCallArg->getOldService())) {
                $this->replaceCallArgValue($node->args[0], $replaceServiceContainerCallArg->getNewService());
                $hasChanged = true;
            }
        }

        return $hasChanged ? $node : null;
    }

    public function configure(array $configuration): void
    {
        Assert::allIsInstanceOf($configuration, ReplaceServiceContainerCallArg::class);

        $this->replaceServiceContainerCallArgs = $configuration;
    }

    /**
     * @param \PhpParser\Node\Expr\ClassConstFetch|string $oldService
     */
    private function isMatchForChangeServiceContainerCallArgValue(Arg $arg, $oldService): bool
    {
        if ($arg->value instanceof ClassConstFetch && $oldService instanceof ClassConstFetch) {
            if ($arg->value->class instanceof Expr || $oldService->class instanceof Expr) {
                return false;
            }

            return $arg->value->class->toString() === $oldService->class->toString();
        } elseif ($arg->value instanceof String_) {
            return $arg->value->value === $oldService;
        }

        return false;
    }

    /**
     * @param \PhpParser\Node\Expr\ClassConstFetch|string $newService
     */
    private function replaceCallArgValue(Arg $arg, $newService): void
    {
        if ($newService instanceof ClassConstFetch) {
            $arg->value = $newService;

            return;
        }

        $arg->value = new String_($newService);
    }

    /**
     * @param \PhpParser\Node\Expr\StaticCall|\PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\FuncCall $node
     */
    private function validMethodCall($node): bool
    {
        if (! $node instanceof MethodCall && ! $node instanceof StaticCall) {
            return false;
        }

        if (! $node->name instanceof Identifier) {
            return false;
        }

        if (! $this->isNames($node->name, ['make', 'get'])) {
            return false;
        }

        switch (true) {
            case $node instanceof MethodCall:
                [$callObject, $class] = [$node->var, 'Illuminate\Contracts\Container\Container'];
                break;
            case $node instanceof StaticCall:
                [$callObject, $class] = [$node->class, 'Illuminate\Support\Facades\Application'];
                break;
        }

        return $this->isObjectType($callObject, new ObjectType($class));
    }

    /**
     * @param \PhpParser\Node\Expr\StaticCall|\PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\FuncCall $node
     */
    private function validFuncCall($node): bool
    {
        if (! $node instanceof FuncCall) {
            return false;
        }

        if (! $node->name instanceof Name) {
            return false;
        }

        return $this->isName($node->name, 'app');
    }
}
