<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Commands;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RectorLaravel\Commands\MakeRuleCommand;

final class MakeRuleCommandTest extends TestCase
{
    private const string TEMP_DIR = __DIR__ . '/../../temp/make-rule-test';

    private MakeRuleCommand $makeRuleCommand;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a temp directory for testing
        if (!is_dir(self::TEMP_DIR)) {
            mkdir(self::TEMP_DIR, 0777, true);
        }

        // Clean any files from previous test runs
        $this->removeDirectory(self::TEMP_DIR);
        mkdir(self::TEMP_DIR, 0777, true);

        $this->makeRuleCommand = new MakeRuleCommand();
    }

    protected function tearDown(): void
    {
        // Clean up after tests
        $this->removeDirectory(self::TEMP_DIR);

        parent::tearDown();
    }

    public function testExecuteCreatesNonConfigurableRule(): void
    {
        // Change to temp directory for file operations
        $originalDir = getcwd();
        chdir(self::TEMP_DIR);

        try {
            // Execute command for non-configurable rule
            $result = $this->makeRuleCommand->execute('TestRule');

            // Assert successful execution
            $this->assertSame(0, $result);

            // Check that expected files were created
            $this->assertFileExists(self::TEMP_DIR . '/src/Rector/TestRuleRector.php');
            $this->assertFileExists(self::TEMP_DIR . '/tests/Rector/TestRuleRector/TestRuleRectorTest.php');
            $this->assertFileExists(self::TEMP_DIR . '/tests/Rector/TestRuleRector/Fixture/some_class.php.inc');
            $this->assertFileExists(self::TEMP_DIR . '/tests/Rector/TestRuleRector/config/configured_rule.php');

            // Check content of main rule file
            $ruleContent = file_get_contents(self::TEMP_DIR . '/src/Rector/TestRuleRector.php');
            $this->assertStringContainsString('namespace RectorLaravel\\Rector;', $ruleContent);
            $this->assertStringContainsString('final class TestRuleRector extends AbstractRector', $ruleContent);
            $this->assertStringNotContainsString('implements ConfigurableRectorInterface', $ruleContent);

            // Check content of config file
            $configContent = file_get_contents(self::TEMP_DIR . '/tests/Rector/TestRuleRector/config/configured_rule.php');
            $this->assertStringContainsString('$rectorConfig->rule(TestRuleRector::class);', $configContent);
            $this->assertStringNotContainsString('$rectorConfig->ruleWithConfiguration', $configContent);
        } finally {
            chdir($originalDir);
        }
    }

    public function testExecuteCreatesConfigurableRule(): void
    {
        // Change to temp directory for file operations
        $originalDir = getcwd();
        chdir(self::TEMP_DIR);

        try {
            // Execute command for configurable rule
            $result = $this->makeRuleCommand->execute('ConfigRule', true);

            // Assert successful execution
            $this->assertSame(0, $result);

            // Check that expected files were created
            $this->assertFileExists(self::TEMP_DIR . '/src/Rector/ConfigRuleRector.php');
            $this->assertFileExists(self::TEMP_DIR . '/tests/Rector/ConfigRuleRector/ConfigRuleRectorTest.php');
            $this->assertFileExists(self::TEMP_DIR . '/tests/Rector/ConfigRuleRector/Fixture/some_class.php.inc');
            $this->assertFileExists(self::TEMP_DIR . '/tests/Rector/ConfigRuleRector/config/configured_rule.php');

            // Check content of main rule file
            $ruleContent = file_get_contents(self::TEMP_DIR . '/src/Rector/ConfigRuleRector.php');
            $this->assertStringContainsString('namespace RectorLaravel\\Rector;', $ruleContent);
            $this->assertStringContainsString('final class ConfigRuleRector extends AbstractRector implements ConfigurableRectorInterface', $ruleContent);
            $this->assertStringContainsString('public function configure(array $configuration): void', $ruleContent);
            $this->assertStringContainsString('ConfiguredCodeSample', $ruleContent);

            // Check content of config file
            $configContent = file_get_contents(self::TEMP_DIR . '/tests/Rector/ConfigRuleRector/config/configured_rule.php');
            $this->assertStringContainsString('$rectorConfig->ruleWithConfiguration(ConfigRuleRector::class, [\'option\' => \'value\']);', $configContent);
        } finally {
            chdir($originalDir);
        }
    }

    public function testExecuteWithDirectoryOption(): void
    {
        // Change to temp directory for file operations
        $originalDir = getcwd();
        chdir(self::TEMP_DIR);

        try {
            // Execute command with directory option
            $result = $this->makeRuleCommand->execute('ClassMethod/ClassRule');

            // Assert successful execution
            $this->assertSame(0, $result);

            // Check that expected files were created in the correct directory
            $this->assertFileExists(self::TEMP_DIR . '/src/Rector/ClassMethod/ClassRuleRector.php');
            $this->assertFileExists(self::TEMP_DIR . '/tests/Rector/ClassMethod/ClassRuleRector/ClassRuleRectorTest.php');
            $this->assertFileExists(self::TEMP_DIR . '/tests/Rector/ClassMethod/ClassRuleRector/Fixture/some_class.php.inc');
            $this->assertFileExists(self::TEMP_DIR . '/tests/Rector/ClassMethod/ClassRuleRector/config/configured_rule.php');

            // Check namespace in the rule file
            $ruleContent = file_get_contents(self::TEMP_DIR . '/src/Rector/ClassMethod/ClassRuleRector.php');
            $this->assertStringContainsString('namespace RectorLaravel\\Rector\\ClassMethod;', $ruleContent);

            // Check namespace in the test file
            $testContent = file_get_contents(self::TEMP_DIR . '/tests/Rector/ClassMethod/ClassRuleRector/ClassRuleRectorTest.php');
            $this->assertStringContainsString('namespace RectorLaravel\\Tests\\Rector\\ClassMethod\\ClassRuleRector;', $testContent);
        } finally {
            chdir($originalDir);
        }
    }

    public function testRuleNameValidation(): void
    {
        // Test with empty rule name
        $result = $this->makeRuleCommand->execute('');
        $this->assertSame(1, $result, 'Empty rule name should be rejected');
    }

    public function testAddingSuffixToRuleName(): void
    {
        // Change to temp directory for file operations
        $originalDir = getcwd();
        chdir(self::TEMP_DIR);

        try {
            // Execute with rule name without "Rector" suffix
            $result = $this->makeRuleCommand->execute('Simple');

            // Assert successful execution
            $this->assertSame(0, $result);

            // Check that files were created with "Rector" suffix
            $this->assertFileExists(self::TEMP_DIR . '/src/Rector/SimpleRector.php');
            $this->assertFileExists(self::TEMP_DIR . '/tests/Rector/SimpleRector/SimpleRectorTest.php');

            // Check that files were not created without suffix
            $this->assertFileDoesNotExist(self::TEMP_DIR . '/src/Rector/Simple.php');
            $this->assertFileDoesNotExist(self::TEMP_DIR . '/tests/Rector/Simple/SimpleTest.php');

            // Execute with rule name already having "Rector" suffix
            $result = $this->makeRuleCommand->execute('CompleteRector');

            // Assert successful execution
            $this->assertSame(0, $result);

            // Check that files were created correctly without duplicating suffix
            $this->assertFileExists(self::TEMP_DIR . '/src/Rector/CompleteRector.php');
            $this->assertFileExists(self::TEMP_DIR . '/tests/Rector/CompleteRector/CompleteRectorTest.php');

            // Check that files were not created with duplicated suffix
            $this->assertFileDoesNotExist(self::TEMP_DIR . '/src/Rector/CompleteRectorRector.php');
        } finally {
            chdir($originalDir);
        }
    }

    /**
     * Helper method to recursively remove a directory and its contents
     */
    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $items = scandir($directory);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . '/' . $item;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }
}
