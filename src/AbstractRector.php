<?php

declare(strict_types=1);

namespace RectorLaravel;

use Rector\Rector\AbstractRector as BaseAbstractRector;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;

abstract class AbstractRector extends BaseAbstractRector implements DocumentedRuleInterface {}
