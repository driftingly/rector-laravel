<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Fixture;

use Nette\Utils\FileSystem;

class SomeClass
{
    public function run()
    {
        return FileSystem::write('file', 'content');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Fixture;

use Nette\Utils\FileSystem;

class SomeClass
{
    public function __construct(private \RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source\TargetFileSystem $targetFileSystem)
    {
    }
    public function run()
    {
        return $this->targetFileSystem->dumpFile('file', 'content');
    }
}

?>
