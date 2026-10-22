<?php

namespace RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Fixture;

use Nette\Utils\FileSystem;
use RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source\ClassWithFileSystemMethod;

class ReUseParentProperty extends ClassWithFileSystemMethod
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
use RectorLaravel\Tests\Rector\StaticCall\StaticCallToMethodCallRector\Source\ClassWithFileSystemMethod;

class ReUseParentProperty extends ClassWithFileSystemMethod
{
    public function run()
    {
        return $this->getSmartFileSystem()->dumpFile('file', 'content');
    }
}

?>
