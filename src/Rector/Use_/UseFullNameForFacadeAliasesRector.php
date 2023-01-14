<?php

declare(strict_types=1);

namespace RectorLaravel\Rector\Use_;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \RectorLaravel\Tests\Rector\Use_\UseFullNameForFacadeAliasesRector\UseFullNameForFacadeAliasesRectorTest
 */
final class UseFullNameForFacadeAliasesRector extends AbstractRector
{
    /**
     * @var array<string, string>
     */
    private array $laravelFacades = [
        'App' => 'Illuminate\Support\Facades\App',
        'Arr' => 'Illuminate\Support\Arr',
        'Artisan' => 'Illuminate\Support\Facades\Artisan',
        'Auth' => 'Illuminate\Support\Facades\Auth',
        'Blade' => 'Illuminate\Support\Facades\Blade',
        'Broadcast' => 'Illuminate\Support\Facades\Broadcast',
        'Bus' => 'Illuminate\Support\Facades\Bus',
        'Cache' => 'Illuminate\Support\Facades\Cache',
        'Config' => 'Illuminate\Support\Facades\Config',
        'Cookie' => 'Illuminate\Support\Facades\Cookie',
        'Crypt' => 'Illuminate\Support\Facades\Crypt',
        'DB' => 'Illuminate\Support\Facades\DB',
        'Model' => 'Illuminate\Database\Eloquent\Model',
        'Event' => 'Illuminate\Support\Facades\Event',
        'File' => 'Illuminate\Support\Facades\File',
        'Gate' => 'Illuminate\Support\Facades\Gate',
        'Hash' => 'Illuminate\Support\Facades\Hash',
        'Lang' => 'Illuminate\Support\Facades\Lang',
        'Log' => 'Illuminate\Support\Facades\Log',
        'Mail' => 'Illuminate\Support\Facades\Mail',
        'Notification' => 'Illuminate\Support\Facades\Notification',
        'Password' => 'Illuminate\Support\Facades\Password',
        'Queue' => 'Illuminate\Support\Facades\Queue',
        'Redirect' => 'Illuminate\Support\Facades\Redirect',
        'Redis' => 'Illuminate\Support\Facades\Redis',
        'Request' => 'Illuminate\Support\Facades\Request',
        'Response' => 'Illuminate\Support\Facades\Response',
        'Route' => 'Illuminate\Support\Facades\Route',
        'Schema' => 'Illuminate\Support\Facades\Schema',
        'Session' => 'Illuminate\Support\Facades\Session',
        'Storage' => 'Illuminate\Support\Facades\Storage',
        'Str' => 'Illuminate\Support\Str',
        'URL' => 'Illuminate\Support\Facades\URL',
        'Validator' => 'Illuminate\Support\Facades\Validator',
        'View' => 'Illuminate\Support\Facades\View',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Use full facade names instead of aliases.',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use DB;
use Auth;
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
        return [Use_::class];
    }

    /**
     * @param Use_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node->uses === []) {
            return null;
        }

        if (! $this->isNames($node->uses[0]->name, array_keys($this->laravelFacades))) {
            return null;
        }

        $node->uses[0]->name = new Name($this->laravelFacades[$node->uses[0]->name->toString()]);

        return $node;
    }
}
