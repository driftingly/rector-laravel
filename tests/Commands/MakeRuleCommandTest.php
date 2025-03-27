<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Commands;

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
        if (! is_dir(self::TEMP_DIR)) {
            mkdir(self::TEMP_DIR, 0777, true);
        }

        // Clean any files from previous test runs
        $this->removeDirectory(self::TEMP_DIR);
        mkdir(self::TEMP_DIR, 0777, true);

        $this->makeRuleCommand = new MakeRuleCommand;

        // Start output buffering
        ob_start();
    }

    protected function tearDown(): void
    {
        // Clean up after tests
        $this->removeDirectory(self::TEMP_DIR);

        // Clean the output buffer
        ob_end_clean();

        parent::tearDown();
    }

    /**
     * @test
     */
    public function execute_creates_non_configurable_rule(): void
    {
        // Change to temp directory for file operations
        $originalDir = (string) getcwd();
        chdir(self::TEMP_DIR);

        try {
            // Execute command for non-configurable rule
            $result = $this->makeRuleCommand->execute('TestRule');

            // Assert successful execution
            self::assertSame(0, $result);

            // Check that expected files were created
            self::assertFileExists(self::TEMP_DIR . '/src/Rector/TestRuleRector.php');
            self::assertFileExists(self::TEMP_DIR . '/tests/Rector/TestRuleRector/TestRuleRectorTest.php');
            self::assertFileExists(self::TEMP_DIR . '/tests/Rector/TestRuleRector/Fixture/some_class.php.inc');
            self::assertFileExists(self::TEMP_DIR . '/tests/Rector/TestRuleRector/config/configured_rule.php');

            // Check content of main rule file
            $ruleContent = (string) file_get_contents(self::TEMP_DIR . '/src/Rector/TestRuleRector.php');
            self::assertStringContainsString('namespace RectorLaravel\\Rector;', $ruleContent);
            self::assertStringContainsString('final class TestRuleRector extends AbstractRector', $ruleContent);
            self::assertStringNotContainsString('implements ConfigurableRectorInterface', $ruleContent);

            // Check content of config file
            $configContent = (string) file_get_contents(self::TEMP_DIR . '/tests/Rector/TestRuleRector/config/configured_rule.php');
            self::assertStringContainsString('$rectorConfig->rule(TestRuleRector::class);', $configContent);
            self::assertStringNotContainsString('$rectorConfig->ruleWithConfiguration', $configContent);
        } finally {
            chdir($originalDir);
        }
    }

    /**
     * @test
     */
    public function execute_creates_configurable_rule(): void
    {
        // Change to temp directory for file operations
        $originalDir = (string) getcwd();
        chdir(self::TEMP_DIR);

        try {
            // Execute command for configurable rule
            $result = $this->makeRuleCommand->execute('ConfigRule', true);

            // Assert successful execution
            self::assertSame(0, $result);

            // Check that expected files were created
            self::assertFileExists(self::TEMP_DIR . '/src/Rector/ConfigRuleRector.php');
            self::assertFileExists(self::TEMP_DIR . '/tests/Rector/ConfigRuleRector/ConfigRuleRectorTest.php');
            self::assertFileExists(self::TEMP_DIR . '/tests/Rector/ConfigRuleRector/Fixture/some_class.php.inc');
            self::assertFileExists(self::TEMP_DIR . '/tests/Rector/ConfigRuleRector/config/configured_rule.php');

            // Check content of main rule file
            $ruleContent = (string) file_get_contents(self::TEMP_DIR . '/src/Rector/ConfigRuleRector.php');
            self::assertStringContainsString('namespace RectorLaravel\\Rector;', $ruleContent);
            self::assertStringContainsString('final class ConfigRuleRector extends AbstractRector implements ConfigurableRectorInterface', $ruleContent);
            self::assertStringContainsString('public function configure(array $configuration): void', $ruleContent);
            self::assertStringContainsString('ConfiguredCodeSample', $ruleContent);

            // Check content of config file
            $configContent = (string) file_get_contents(self::TEMP_DIR . '/tests/Rector/ConfigRuleRector/config/configured_rule.php');
            self::assertStringContainsString('$rectorConfig->ruleWithConfiguration(ConfigRuleRector::class, [\'option\' => \'value\']);', $configContent);
        } finally {
            chdir($originalDir);
        }
    }

    /**
     * @test
     */
    public function execute_with_directory_option(): void
    {
        // Change to temp directory for file operations
        $originalDir = (string) getcwd();
        chdir(self::TEMP_DIR);

        try {
            // Execute command with directory option
            $result = $this->makeRuleCommand->execute('ClassMethod/ClassRule');

            // Assert successful execution
            self::assertSame(0, $result);

            // Check that expected files were created in the correct directory
            self::assertFileExists(self::TEMP_DIR . '/src/Rector/ClassMethod/ClassRuleRector.php');
            self::assertFileExists(self::TEMP_DIR . '/tests/Rector/ClassMethod/ClassRuleRector/ClassRuleRectorTest.php');
            self::assertFileExists(self::TEMP_DIR . '/tests/Rector/ClassMethod/ClassRuleRector/Fixture/some_class.php.inc');
            self::assertFileExists(self::TEMP_DIR . '/tests/Rector/ClassMethod/ClassRuleRector/config/configured_rule.php');

            // Check namespace in the rule file
            $ruleContent = (string) file_get_contents(self::TEMP_DIR . '/src/Rector/ClassMethod/ClassRuleRector.php');
            self::assertStringContainsString('namespace RectorLaravel\\Rector\\ClassMethod;', $ruleContent);

            // Check namespace in the test file
            $testContent = (string) file_get_contents(self::TEMP_DIR . '/tests/Rector/ClassMethod/ClassRuleRector/ClassRuleRectorTest.php');
            self::assertStringContainsString('namespace RectorLaravel\\Tests\\Rector\\ClassMethod\\ClassRuleRector;', $testContent);
        } finally {
            chdir($originalDir);
        }
    }

    /**
     * @test
     */
    public function rule_name_validation(): void
    {
        // Test with empty rule name
        $result = $this->makeRuleCommand->execute('');
        self::assertSame(1, $result, 'Empty rule name should be rejected');
    }

    /**
     * @test
     */
    public function rule_name_does_not_need_suffix(): void
    {
        // Change to temp directory for file operations
        $originalDir = (string) getcwd();
        chdir(self::TEMP_DIR);

        try {
            // Execute with rule name without "Rector" suffix
            $result = $this->makeRuleCommand->execute('Simple');

            // Assert successful execution
            self::assertSame(0, $result);

            // Check that files were created with "Rector" suffix
            self::assertFileExists(self::TEMP_DIR . '/src/Rector/SimpleRector.php');
            self::assertFileExists(self::TEMP_DIR . '/tests/Rector/SimpleRector/SimpleRectorTest.php');

            // Check that files were not created without suffix
            self::assertFileDoesNotExist(self::TEMP_DIR . '/src/Rector/Simple.php');
            self::assertFileDoesNotExist(self::TEMP_DIR . '/tests/Rector/Simple/SimpleTest.php');
        } finally {
            chdir($originalDir);
        }
    }

    /**
     * @test
     */
    public function rule_name_can_have_valid_suffix(): void
    {
        // Change to temp directory for file operations
        $originalDir = (string) getcwd();
        chdir(self::TEMP_DIR);

        try {
            // Execute with rule name already having "Rector" suffix
            $result = $this->makeRuleCommand->execute('CompleteRector');

            // Assert successful execution
            self::assertSame(0, $result);

            // Check that files were created correctly without duplicating suffix
            self::assertFileExists(self::TEMP_DIR . '/src/Rector/CompleteRector.php');
            self::assertFileExists(self::TEMP_DIR . '/tests/Rector/CompleteRector/CompleteRectorTest.php');

            // Check that files were not created with duplicated suffix
            self::assertFileDoesNotExist(self::TEMP_DIR . '/src/Rector/CompleteRectorRector.php');
        } finally {
            chdir($originalDir);
        }
    }

    /**
     * Helper method to recursively remove a directory and its contents
     */
    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
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
