<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\Fixture\RemoveDirectObserveAndKeepOtherBootStatements;

final class AppServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);
        $this->bootSomethingElse();
    }

    private function bootSomethingElse(): void
    {
    }
}

final class UserObserver
{
}

?>
-----
<?php

declare(strict_types=1);

namespace RectorLaravel\Tests\Rector\ClassMethod\RemoveModelObserveCallsFromBootRector\Fixture\RemoveDirectObserveAndKeepOtherBootStatements;

final class AppServiceProvider
{
    public function boot(): void
    {
        $this->bootSomethingElse();
    }

    private function bootSomethingElse(): void
    {
    }
}

final class UserObserver
{
}

?>
