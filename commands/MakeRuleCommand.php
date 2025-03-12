<?php

declare(strict_types=1);

namespace RectorLaravel\Commands;

use Nette\Utils\FileSystem;
use Rector\Console\Command\CustomRuleCommand;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Generates scaffolding for a new Rector rule
 */
final class MakeRuleCommand
{
    private bool $configurable = false;
    private ?string $directory = null;

    private const TEMPLATE_DIR = __DIR__ . '/../templates/new-rule';
    private const CONFIGURABLE_TEMPLATE_DIR = __DIR__ . '/../templates/new-rule/configurable';
    private const NON_CONFIGURABLE_TEMPLATE_DIR = __DIR__ . '/../templates/new-rule/non-configurable';

    public function execute(string $ruleName, array $options = []): int
    {
        $this->configurable = $options['configurable'] ?? false;
        $this->directory = $options['directory'] ?? null;
        
        return $this->executeGeneration($ruleName);
    }

    private function executeGeneration(string $ruleName): int
    {
        // Validate rule name
        if (!$this->validateRuleName($ruleName)) {
            echo PHP_EOL . 'Invalid rule name. Rule name must be in PascalCase and not empty.' . PHP_EOL;
            return 1;
        }

        $rectorName = $this->getRuleName($ruleName);
        $rulesNamespace = 'RectorLaravel\\Rector';
        $testsNamespace = 'RectorLaravel\\Tests\\Rector';

        // Add directory to namespace if provided
        if ($this->directory !== null) {
            // Clean directory name (ensure it ends with a backslash for paths but not for namespace)
            $cleanDir = rtrim($this->directory, '\\/');
            $this->directory = $cleanDir . '/';
            $namespaceDir = str_replace('/', '\\', $cleanDir);
            $rulesNamespace .= '\\' . $namespaceDir;
            $testsNamespace .= '\\' . $namespaceDir;
        }

        echo PHP_EOL . "Using namespaces:" . PHP_EOL;
        echo "  Rule namespace: $rulesNamespace" . PHP_EOL;
        echo "  Test namespace: $testsNamespace" . PHP_EOL . PHP_EOL;

        $currentDirectory = getcwd();
        $generatedFilePaths = [];

        // Select template directory based on configurable flag
        $templateDir = $this->configurable ? self::CONFIGURABLE_TEMPLATE_DIR : self::NON_CONFIGURABLE_TEMPLATE_DIR;
        
        // Fall back to standard template directory if specific directory does not exist
        if (!is_dir($templateDir)) {
            $templateDir = self::TEMPLATE_DIR;
        }

        // Generate the rule class file
        $ruleTemplateFile = $templateDir . '/src/Rector/__NAME__.php';
        if (!file_exists($ruleTemplateFile)) {
            $ruleTemplateFile = self::TEMPLATE_DIR . '/src/Rector/__NAME__.php';
        }
        
        if (file_exists($ruleTemplateFile)) {
            $contents = file_get_contents($ruleTemplateFile);
            
            $newContent = $this->replaceNameVariable($rectorName, $contents);
            $newContent = $this->replaceNamespaceVariable($rulesNamespace, $newContent);
            $newContent = $this->replaceTestsNamespaceVariable($testsNamespace, $newContent);
            
            // Create the rule file path
            $ruleFilePath = 'src/Rector/';
            if ($this->directory !== null) {
                $ruleFilePath .= $this->directory;
            }
            $ruleFilePath .= $rectorName . '.php';
            
            // Ensure directory exists
            $this->ensureDirectoryExists($currentDirectory . '/' . dirname($ruleFilePath));
            FileSystem::write($currentDirectory . '/' . $ruleFilePath, $newContent, null);
            $generatedFilePaths[] = $ruleFilePath;
        }

        // Create test directory structure
        $testDir = 'tests/Rector/';
        if ($this->directory !== null) {
            $testDir .= $this->directory;
        }
        $testDir .= $rectorName;
            
        // Create test file
        $testTemplateFile = $templateDir . '/tests/Rector/__NAME__/__NAME__Test.php';
        if (!file_exists($testTemplateFile)) {
            $testTemplateFile = self::TEMPLATE_DIR . '/tests/Rector/__NAME__/__NAME__Test.php';
        }
        
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
        $fixtureTemplateFile = $templateDir . '/tests/Rector/__NAME__/Fixture/some_class.php.inc';
        if (!file_exists($fixtureTemplateFile)) {
            $fixtureTemplateFile = self::TEMPLATE_DIR . '/tests/Rector/__NAME__/Fixture/some_class.php.inc';
        }
        
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
            
        // Create config file from template - use the appropriate template based on configurable flag
        $configTemplateFile = $templateDir . '/tests/Rector/__NAME__/config/configured_rule.php';
        if (!file_exists($configTemplateFile)) {
            $configTemplateFile = self::TEMPLATE_DIR . '/tests/Rector/__NAME__/config/configured_rule.php';
        }
        
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

    private function validateRuleName(string $ruleName): bool
    {
        if (empty($ruleName)) {
            return false;
        }

        // Check if PascalCase (first character uppercase)
        if (!ctype_upper($ruleName[0])) {
            return false;
        }

        return true;
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
