<?php

namespace MasterDmx\LaravelRelinking\ConsoleCommands;

class RelinkingReset extends Command
{
    protected $signature = 'relinking:reset';

    protected $description = 'Removes all linked models and relationships';

    public function handle()
    {
        $this->relinking->reset();

        $this->info('Successful reset');

        return 0;
    }
}
