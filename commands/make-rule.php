#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

// get the first argument, the rule name
// make sure we have at least one argument
if ($argc < 2) {
    echo PHP_EOL . 'Please provide the name of the rule!' . PHP_EOL;
    exit(1);
}

$ruleName = $argv[1];

$command = new \RectorLaravel\Commands\MakeRuleCommand;
exit($command->execute($ruleName));
