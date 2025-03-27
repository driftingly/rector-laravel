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
    private const string TEMPLATE_DIR = __DIR__ . '/../templates';

    private bool $configurable = false;
    private ?string $directory = null;
    private string $rulesNamespace = 'RectorLaravel\\Rector';
    private string $testsNamespace = 'RectorLaravel\\Tests\\Rector';
    private string $testDir = 'tests/Rector/';
    private string $currentDirectory;

    public function execute(string $ruleName, bool $configurable = false): int
    {
        $this->configurable = $configurable;

        // Validate rule name
        if (empty($ruleName)) {
            echo PHP_EOL . 'Rule name must not be empty.' . PHP_EOL;

            return 1;
        }

        $this->currentDirectory = (string) getcwd();

        $rectorName = $this->setNamespacesAndDirectories($ruleName);

        $generatedFilePaths = [
            $this->createRule($rectorName),
            $this->createTest($rectorName),
            $this->createTestFixture($rectorName),
            $this->createTestConfig($rectorName),
        ];

        echo 'Generated files:' . PHP_EOL . PHP_EOL . "\t";
        echo implode(PHP_EOL . "\t", array_filter($generatedFilePaths)) . PHP_EOL;

        return 0;
    }

    private function setNamespacesAndDirectories(string $ruleName): string
    {
        // Extract directory and rule name if format contains slashes like "Directory/SubDir/More/RuleName"
        if (str_contains($ruleName, '/')) {
            $parts = explode('/', $ruleName);
            // The last part is the rule name
            $ruleName = array_pop($parts);
            // All other parts form the directory path
            if (! empty($parts)) {
                $this->directory = implode('/', $parts) . '/';
            }
        }

        // Add directory to namespace if provided
        if ($this->directory !== null) {
            // Clean directory name (ensure it ends with a backslash for paths but not for namespace)
            $cleanDir = rtrim($this->directory, '\\/');
            $this->directory = $cleanDir . '/';
            $namespaceDir = str_replace('/', '\\', $cleanDir);
            $this->rulesNamespace .= '\\' . $namespaceDir;
            $this->testsNamespace .= '\\' . $namespaceDir;
        }

        $rectorName = $this->formatRuleName($ruleName);

        $this->testDir .= $this->directory . $rectorName;

        return $rectorName;
    }

    private function createRule(string $rectorName): ?string
    {
        $ruleFilePath = null;
        $ruleTemplateFile = $this->configurable
            ? self::TEMPLATE_DIR . '/configurable-rule.php.template'
            : self::TEMPLATE_DIR . '/non-configurable-rule.php.template';

        if (file_exists($ruleTemplateFile)) {
            $contents = file_get_contents($ruleTemplateFile);

            $newContent = $this->replaceNameVariable($rectorName, $contents);
            $newContent = $this->replaceNamespaceVariable($newContent);
            $newContent = $this->replaceTestsNamespaceVariable($newContent);

            // Create the rule file path
            $ruleFilePath = 'src/Rector/';
            if ($this->directory !== null) {
                $ruleFilePath .= $this->directory;
            }
            $ruleFilePath .= $rectorName . '.php';

            // Ensure directory exists
            $this->ensureDirectoryExists($this->currentDirectory . '/' . dirname($ruleFilePath));
            FileSystem::write($this->currentDirectory . '/' . $ruleFilePath, $newContent, null);
        }

        return $ruleFilePath;
    }

    private function createTest(string $rectorName): ?string
    {
        $testFilePath = null;
        $testTemplateFile = self::TEMPLATE_DIR . '/test.php.template';

        if (file_exists($testTemplateFile)) {
            $contents = file_get_contents($testTemplateFile);
            $newContent = $this->replaceNameVariable($rectorName, $contents);
            $newContent = $this->replaceNamespaceVariable($newContent);
            $newContent = $this->replaceTestsNamespaceVariable($newContent);

            $testFilePath = $this->testDir . '/' . $rectorName . 'Test.php';
            $this->ensureDirectoryExists($this->currentDirectory . '/' . dirname($testFilePath));
            FileSystem::write($this->currentDirectory . '/' . $testFilePath, $newContent, null);
        }

        return $testFilePath;
    }

    private function createTestFixture(string $rectorName): ?string
    {
        $fixtureFilePath = null;
        $fixtureDir = $this->testDir . '/Fixture';
        $this->ensureDirectoryExists($this->currentDirectory . '/' . $fixtureDir);

        // Create fixture file from template
        $fixtureTemplateFile = self::TEMPLATE_DIR . '/fixture.php.inc.template';

        if (file_exists($fixtureTemplateFile)) {
            $fixtureContents = file_get_contents($fixtureTemplateFile);
            $fixtureContents = $this->replaceNameVariable($rectorName, $fixtureContents);
            $fixtureContents = $this->replaceTestsNamespaceVariable($fixtureContents);

            $fixtureFilePath = $fixtureDir . '/some_class.php.inc';
            FileSystem::write($this->currentDirectory . '/' . $fixtureFilePath, $fixtureContents, null);
        }

        return $fixtureFilePath;
    }

    private function createTestConfig(string $rectorName): ?string
    {
        $configFilePath = null;
        $configDir = $this->testDir . '/config';
        $this->ensureDirectoryExists($this->currentDirectory . '/' . $configDir);

        // Create config file from template - select based on configurability
        $configTemplateFile = $this->configurable
            ? self::TEMPLATE_DIR . '/configurable-config.php.template'
            : self::TEMPLATE_DIR . '/non-configurable-config.php.template';

        if (file_exists($configTemplateFile)) {
            $configContents = file_get_contents($configTemplateFile);
            $configContents = $this->replaceNameVariable($rectorName, $configContents);
            $configContents = $this->replaceNamespaceVariable($configContents);

            $configFilePath = $configDir . '/configured_rule.php';
            FileSystem::write($this->currentDirectory . '/' . $configFilePath, $configContents, null);
        }

        return $configFilePath;
    }

    private function formatRuleName(string $ruleName): string
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

    private function replaceNamespaceVariable(string $contents): string
    {
        return str_replace('__NAMESPACE__', $this->rulesNamespace, $contents);
    }

    private function replaceTestsNamespaceVariable(string $contents): string
    {
        return str_replace('__TESTS_NAMESPACE__', $this->testsNamespace, $contents);
    }

    private function ensureDirectoryExists(string $directory): void
    {
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }
}
