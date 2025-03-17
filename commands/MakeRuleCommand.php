<?php

declare(strict_types=1);

namespace RectorLaravel\Commands;

use Nette\Utils\FileSystem;
use Rector\Console\Command\CustomRuleCommand;

/**
 * Generates scaffolding for a new Rector rule
 *
 * Modified version of
 *
 * @see CustomRuleCommand
 */
final class MakeRuleCommand
{
    private bool $configurable = false;

    private const string TEMPLATE_DIR = __DIR__ . '/../templates';

    public function execute(string $ruleName, bool $configurable = false): int
    {
        $this->configurable = $configurable;

        return $this->generateFiles($ruleName);
    }

    private function generateFiles(string $ruleName): int
    {
        // Validate rule name
        if (empty($ruleName)) {
            echo PHP_EOL . 'Rule name must not be empty.' . PHP_EOL;
            return 1;
        }

        // Extract directory and rule name if format contains slashes like "Directory/SubDir/More/RuleName"
        $directory = null;
        if (str_contains($ruleName, '/')) {
            $parts = explode('/', $ruleName);
            // The last part is the rule name
            $ruleName = array_pop($parts);
            // All other parts form the directory path
            if (!empty($parts)) {
                $directory = implode('/', $parts) . '/';
            }
        }

        $rectorName = $this->getRuleName($ruleName);
        $rulesNamespace = 'RectorLaravel\\Rector';
        $testsNamespace = 'RectorLaravel\\Tests\\Rector';

        // Add directory to namespace if provided
        if ($directory !== null) {
            // Clean directory name (ensure it ends with a backslash for paths but not for namespace)
            $cleanDir = rtrim($directory, '\\/');
            $directory = $cleanDir . '/';
            $namespaceDir = str_replace('/', '\\', $cleanDir);
            $rulesNamespace .= '\\' . $namespaceDir;
            $testsNamespace .= '\\' . $namespaceDir;
        }

        $currentDirectory = getcwd();
        $generatedFilePaths = [];

        // Generate the rule class file - select the appropriate template based on configurability
        $ruleTemplateFile = $this->configurable
            ? self::TEMPLATE_DIR . '/configurable-rule.php.template'
            : self::TEMPLATE_DIR . '/non-configurable-rule.php.template';

        if (file_exists($ruleTemplateFile)) {
            $contents = file_get_contents($ruleTemplateFile);

            $newContent = $this->replaceNameVariable($rectorName, $contents);
            $newContent = $this->replaceNamespaceVariable($rulesNamespace, $newContent);
            $newContent = $this->replaceTestsNamespaceVariable($testsNamespace, $newContent);

            // Create the rule file path
            $ruleFilePath = 'src/Rector/';
            if ($directory !== null) {
                $ruleFilePath .= $directory;
            }
            $ruleFilePath .= $rectorName . '.php';

            // Ensure directory exists
            $this->ensureDirectoryExists($currentDirectory . '/' . dirname($ruleFilePath));
            FileSystem::write($currentDirectory . '/' . $ruleFilePath, $newContent, null);
            $generatedFilePaths[] = $ruleFilePath;
        }

        // Create test directory structure
        $testDir = 'tests/Rector/';
        if ($directory !== null) {
            $testDir .= $directory;
        }
        $testDir .= $rectorName;

        // Create test file
        $testTemplateFile = self::TEMPLATE_DIR . '/test.php.template';

        if (file_exists($testTemplateFile)) {
            $contents = file_get_contents($testTemplateFile);
            $newContent = $this->replaceNameVariable($rectorName, $contents);
            $newContent = $this->replaceNamespaceVariable($rulesNamespace, $newContent);
            $newContent = $this->replaceTestsNamespaceVariable($testsNamespace, $newContent);

            $testFilePath = $testDir . '/' . $rectorName . 'Test.php';
            $this->ensureDirectoryExists($currentDirectory . '/' . dirname($testFilePath));
            FileSystem::write($currentDirectory . '/' . $testFilePath, $newContent, null);
            $generatedFilePaths[] = $testFilePath;
        }

        // Create fixture directory
        $fixtureDir = $testDir . '/Fixture';
        $this->ensureDirectoryExists($currentDirectory . '/' . $fixtureDir);

        // Create fixture file from template
        $fixtureTemplateFile = self::TEMPLATE_DIR . '/fixture.php.inc.template';

        if (file_exists($fixtureTemplateFile)) {
            $fixtureContents = file_get_contents($fixtureTemplateFile);
            $fixtureContents = $this->replaceNameVariable($rectorName, $fixtureContents);
            $fixtureContents = $this->replaceTestsNamespaceVariable($testsNamespace, $fixtureContents);

            $fixtureFilePath = $fixtureDir . '/some_class.php.inc';
            FileSystem::write($currentDirectory . '/' . $fixtureFilePath, $fixtureContents, null);
            $generatedFilePaths[] = $fixtureFilePath;
        }

        // Create config directory
        $configDir = $testDir . '/config';
        $this->ensureDirectoryExists($currentDirectory . '/' . $configDir);

        // Create config file from template - select based on configurability
        $configTemplateFile = $this->configurable
            ? self::TEMPLATE_DIR . '/configurable-config.php.template'
            : self::TEMPLATE_DIR . '/non-configurable-config.php.template';

        if (file_exists($configTemplateFile)) {
            $configContents = file_get_contents($configTemplateFile);
            $configContents = $this->replaceNameVariable($rectorName, $configContents);
            $configContents = $this->replaceNamespaceVariable($rulesNamespace, $configContents);

            $configFilePath = $configDir . '/configured_rule.php';
            FileSystem::write($currentDirectory . '/' . $configFilePath, $configContents, null);
            $generatedFilePaths[] = $configFilePath;
        }

        echo 'Generated files:' . PHP_EOL . PHP_EOL . "\t";
        echo implode(PHP_EOL . "\t", $generatedFilePaths) . PHP_EOL;

        return 0;
    }

    private function getRuleName(string $ruleName): string
    {
        if (! str_ends_with($ruleName, 'Rector')) {
            $ruleName .= 'Rector';
        }

        return ucfirst($ruleName);
    }

    private function replaceNameVariable(string $rectorName, string $contents): string
    {
        return str_replace('__NAME__', $rectorName, $contents);
    }

    private function replaceNamespaceVariable(string $namespace, string $contents): string
    {
        return str_replace('__NAMESPACE__', $namespace, $contents);
    }

    private function replaceTestsNamespaceVariable(string $testsNamespace, string $contents): string
    {
        return str_replace('__TESTS_NAMESPACE__', $testsNamespace, $contents);
    }

    private function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }
}
