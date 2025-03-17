#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

// Parse command line arguments
$configurable = false;
$ruleName = '';

// Parse options
foreach ($argv as $i => $arg) {
    if ($i === 0) continue; // Skip script name

    if ($arg === '--configurable' || $arg === '-c') {
        $configurable = true;
    } elseif (empty($ruleName) && !str_starts_with($arg, '-')) {
        // If it's not an option, and we don't have a rule name yet, it's the rule name
        $ruleName = $arg;
    }
}

// If no rule name provided, show usage
if (empty($ruleName)) {
    echo PHP_EOL . 'Please provide the name of the rule!' . PHP_EOL;
    echo 'Usage: php commands/make-rule.php [options] RuleName' . PHP_EOL;
    echo 'Usage with directory: php commands/make-rule.php [options] Directory/RuleName' . PHP_EOL;
    echo 'Options:' . PHP_EOL;
    echo '  --configurable, -c                Create a configurable rule' . PHP_EOL;
    exit(1);
}

$command = new \RectorLaravel\Commands\MakeRuleCommand;
exit($command->execute($ruleName, $configurable));
