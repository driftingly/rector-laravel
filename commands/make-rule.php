#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

// For debugging purposes, output all arguments
echo "Command arguments:" . PHP_EOL;
for ($i = 0; $i < $argc; $i++) {
    echo "  $i: {$argv[$i]}" . PHP_EOL;
}
echo PHP_EOL;

// Parse command line arguments
$options = [
    'configurable' => false,
    'directory' => null,
];

$ruleName = '';

// Parse options - simpler approach
foreach ($argv as $i => $arg) {
    if ($i === 0) continue; // Skip script name
    
    if ($arg === '--configurable' || $arg === '-c') {
        $options['configurable'] = true;
    } elseif (preg_match('/^--directory=(.+)$/', $arg, $matches)) {
        $options['directory'] = $matches[1];
    } elseif ($arg === '--directory' || $arg === '-d') {
        if (isset($argv[$i + 1]) && !str_starts_with($argv[$i + 1], '-')) {
            $options['directory'] = $argv[$i + 1];
            // Skip the next argument as it's already consumed
            $argv[$i + 1] = '--skip--';
        }
    } elseif ($arg !== '--skip--' && empty($ruleName) && !str_starts_with($arg, '-')) {
        // If it's not an option or skipped, and we don't have a rule name yet, it's the rule name
        $ruleName = $arg;
    }
}

// Debug info - print the parsed options
echo "Parsed options:" . PHP_EOL;
echo "  Rule name: $ruleName" . PHP_EOL;
echo "  Configurable: " . ($options['configurable'] ? 'yes' : 'no') . PHP_EOL;
echo "  Directory: " . ($options['directory'] ?? 'none') . PHP_EOL . PHP_EOL;

// If no rule name provided, show usage
if (empty($ruleName)) {
    echo PHP_EOL . 'Please provide the name of the rule!' . PHP_EOL;
    echo 'Usage: php commands/make-rule.php [options] RuleName' . PHP_EOL;
    echo 'Options:' . PHP_EOL;
    echo '  --configurable, -c                Create a configurable rule' . PHP_EOL;
    echo '  --directory=DIR, -d DIR           Place rule in a subdirectory of src/Rector (e.g. If_)' . PHP_EOL;
    exit(1);
}

$command = new \RectorLaravel\Commands\MakeRuleCommand;
exit($command->execute($ruleName, $options));
