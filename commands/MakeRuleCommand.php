<?php

declare(strict_types=1);

namespace RectorLaravel\Commands;

use Nette\Utils\FileSystem;
use Rector\Console\Command\CustomRuleCommand;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Modified version of
 *
 * @see CustomRuleCommand
 */
final class MakeRuleCommand
{
    public function execute(string $ruleName): int
    {
        $rectorName = $this->getRuleName($ruleName);
        $rulesNamespace = 'RectorLaravel\\Rector';
        $testsNamespace = 'RectorLaravel\\Tests\\Rector';

        // find all files in templates directory
        $finder = Finder::create()
            ->files()
            ->in(__DIR__ . '/../templates/new-rule')
            ->notName('__NAME__Test.php');

        $finder->append([
            new SplFileInfo(
                __DIR__ . '/../templates/new-rule/tests/Rector/__NAME__/__NAME__Test.php',
                'tests/Rector/__NAME__',
                'tests/Rector/__NAME__/__NAME__Test.php',
            ),
        ]);

        $currentDirectory = getcwd();

        $generatedFilePaths = [];

        $fileInfos = iterator_to_array($finder->getIterator());

        foreach ($fileInfos as $fileInfo) {
            $newContent = $this->replaceNameVariable($rectorName, $fileInfo->getContents());
            $newContent = $this->replaceNamespaceVariable($rulesNamespace, $newContent);
            $newContent = $this->replaceTestsNamespaceVariable($testsNamespace, $newContent);
            $newFilePath = $this->replaceNameVariable($rectorName, $fileInfo->getRelativePathname());

            FileSystem::write($currentDirectory . '/' . $newFilePath, $newContent, null);

            $generatedFilePaths[] = $newFilePath;
        }

        echo PHP_EOL . 'Generated files:' . PHP_EOL . PHP_EOL . "\t";
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
}
