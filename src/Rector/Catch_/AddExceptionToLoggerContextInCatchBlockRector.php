<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Catch_;

use PhpParser\Node\Identifier;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeVisitor;
use PHPStan\Type\ObjectType;
use Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser;
use Rector\RuleDoc\CodeSample\CodeSample;
use Rector\RuleDoc\RuleDefinition;
use RectorLaravel\AbstractRector;
use RectorLaravel\Tests\Rector\Catch_\AddExceptionToLoggerContextInCatchBlockRector\AddExceptionToLoggerContextInCatchBlockRectorTest;

use function count;
use function in_array;
use function is_string;

/**
 * PSR-3 requires an exception passed in the logger context to be under the `exception` key. Monolog
 * handlers of Sentry, Bugsnag, Rollbar, Honeybadger, Flare and New Relic only read that key: under
 * another key, or when it is missing, they report a plain message without the exception class and
 * its stack trace. See https://www.php-fig.org/psr/psr-3/#13-context
 *
 * The rule is not part of any set. It only rewrites calls it can prove safe:
 *
 * - the receiver is a `Psr\Log\LoggerInterface` or the `Log` facade, and the method is one of the
 *   PSR-3 logging methods (`error()`, `log()`...);
 * - the context is missing or an array literal. A context held in a variable is left alone because
 *   the same array may be used for something else than logging;
 * - an existing `exception` key is never touched, whatever its value.
 *
 * Calls inside a closure, an arrow function or a nested catch block are not rewritten with the
 * outer exception.
 *
 * @see AddExceptionToLoggerContextInCatchBlockRectorTest
 */
final class AddExceptionToLoggerContextInCatchBlockRector extends AbstractRector
{
    private const string EXCEPTION_KEY = 'exception';

    private const array LEVEL_METHODS = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];

    private const string LOG_METHOD = 'log';

    private const string LOG_FACADE = 'Illuminate\Support\Facades\Log';

    private const string LOGGER_INTERFACE = 'Psr\Log\LoggerInterface';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Pass the caught exception under the "exception" key of the logger context in catch blocks, as PSR-3 requires, so that error trackers such as Sentry can report its stack trace',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
try {
    $this->import();
} catch (Exception $e) {
    $this->logger->error('Import failed');
    Log::warning('Import failed', ['file' => $file]);
    $this->logger->error('Import failed', ['error' => $e]);
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
try {
    $this->import();
} catch (Exception $e) {
    $this->logger->error('Import failed', ['exception' => $e]);
    Log::warning('Import failed', ['file' => $file, 'exception' => $e]);
    $this->logger->error('Import failed', ['exception' => $e]);
}
CODE_SAMPLE
                ),
            ],
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Catch_::class];
    }

    public function refactor(Node $node): ?Catch_
    {
        if (! $node instanceof Catch_) {
            return null;
        }

        $caughtVariable = $node->var;
        if (! $caughtVariable instanceof Variable || ! is_string($caughtVariable->name)) {
            return null;
        }

        $hasChanged = false;
        foreach ($this->findLoggerCalls($node) as $loggerCall) {
            if ($this->addExceptionToContext($loggerCall, $caughtVariable)) {
                $hasChanged = true;
            }
        }

        return $hasChanged ? $node : null;
    }

    /**
     * @return list<MethodCall|StaticCall>
     */
    private function findLoggerCalls(Catch_ $catch): array
    {
        $loggerCalls = [];

        SimpleCallableNodeTraverser::traverseNodesWithCallable($catch->stmts, function (Node $node) use (&$loggerCalls) {
            // nested catch blocks are visited on their own, with their own exception
            if ($node instanceof Catch_ || $node instanceof FunctionLike || $node instanceof ClassLike) {
                return NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }

            if (($node instanceof MethodCall || $node instanceof StaticCall) && $this->isLoggerCall($node)) {
                $loggerCalls[] = $node;
            }

            return null;
        });

        return $loggerCalls;
    }

    private function isLoggerCall(MethodCall|StaticCall $call): bool
    {
        if ($this->resolveContextPosition($call) === null) {
            return false;
        }

        if ($call instanceof MethodCall) {
            return $this->isObjectType($call->var, new ObjectType(self::LOGGER_INTERFACE));
        }

        return $this->isObjectType($call->class, new ObjectType(self::LOG_FACADE));
    }

    /**
     * Position of the context argument: `log($level, $message, $context)`, `error($message, $context)`...
     */
    private function resolveContextPosition(MethodCall|StaticCall $call): ?int
    {
        $methodName = $this->getName($call->name);
        if ($methodName === self::LOG_METHOD) {
            return 2;
        }

        if (in_array($methodName, self::LEVEL_METHODS, true)) {
            return 1;
        }

        return null;
    }

    private function addExceptionToContext(MethodCall|StaticCall $call, Variable $caughtVariable): bool
    {
        $contextPosition = $this->resolveContextPosition($call);
        if ($contextPosition === null) {
            return false;
        }

        foreach ($call->args as $arg) {
            if (! $arg instanceof Arg || $arg->unpack || $arg->name instanceof Identifier) {
                return false;
            }
        }

        $argCount = count($call->args);
        if ($argCount < $contextPosition || $argCount > $contextPosition + 1) {
            return false;
        }

        if ($argCount === $contextPosition) {
            $call->args[] = new Arg(new Array_([$this->createExceptionItem($caughtVariable)]));

            return true;
        }

        $contextArg = $call->args[$contextPosition];
        if (! $contextArg->value instanceof Array_) {
            return false;
        }

        return $this->completeContextArray($contextArg->value, $caughtVariable);
    }

    private function completeContextArray(Array_ $context, Variable $caughtVariable): bool
    {
        foreach ($context->items as $item) {
            // `...$context` may already hold the exception
            if ($item->unpack) {
                return false;
            }

            if ($item->key instanceof String_ && $item->key->value === self::EXCEPTION_KEY) {
                return false;
            }
        }

        foreach ($context->items as $item) {
            if ($this->isSameVariable($item->value, $caughtVariable)) {
                $item->key = new String_(self::EXCEPTION_KEY);

                return true;
            }
        }

        $context->items[] = $this->createExceptionItem($caughtVariable);

        return true;
    }

    private function isSameVariable(Node $node, Variable $caughtVariable): bool
    {
        return $node instanceof Variable && $node->name === $caughtVariable->name;
    }

    private function createExceptionItem(Variable $caughtVariable): ArrayItem
    {
        return new ArrayItem(new Variable($caughtVariable->name), new String_(self::EXCEPTION_KEY));
    }
}
