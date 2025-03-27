#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

// Parse command line arguments
$configurable = false;
$ruleName = '';

// Parse options
foreach ($argv as $i => $arg) {
    if ($i === 0) {
        continue;
    } // Skip script name

    if ($arg === '--configurable' || $arg === '-c') {
        $configurable = true;
    } elseif (empty($ruleName) && ! str_starts_with($arg, '-')) {
        // If it's not an option, and we don't have a rule name yet, it's the rule name
        $ruleName = $arg;
    }
}

$command = new \RectorLaravel\Commands\MakeRuleCommand;
exit($command->execute($ruleName, $configurable));
