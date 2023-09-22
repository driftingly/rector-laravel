<?php

namespace RectorLaravel\Tests\Rector\MethodCall\UseComponentPropertyWithinCommandsRector\Fixture;

use Illuminate\Console\Command;

class CommandWithComponents extends Command
{
    public function handle()
    {
        $this->ask('What is your name?');
        $this->line('Thank you!');
        $this->info('Thank you!');
        $this->error('Thank you!');
        $this->warn('Thank you!');
        $this->confirm('Thank you!');
        $this->askWithCompletion('Thank you!');
        $this->choice('Thank you!');
        $this->alert('Thank you!');
    }

    public function test()
    {
        $this->ask('What is your name?');
    }
}

?>
-----
<?php

namespace RectorLaravel\Tests\Rector\MethodCall\UseComponentPropertyWithinCommandsRector\Fixture;

use Illuminate\Console\Command;

class CommandWithComponents extends Command
{
    public function handle()
    {
        $this->components->ask('What is your name?');
        $this->components->line('Thank you!');
        $this->components->info('Thank you!');
        $this->components->error('Thank you!');
        $this->components->warn('Thank you!');
        $this->components->confirm('Thank you!');
        $this->components->askWithCompletion('Thank you!');
        $this->components->choice('Thank you!');
        $this->components->alert('Thank you!');
    }

    public function test()
    {
        $this->components->ask('What is your name?');
    }
}

?>
