<?php

namespace MasterDmx\LaravelRelinking\ConsoleCommands;

class RelinkingGenerate extends Command
{
    protected $signature = 'relinking:generate';

    protected $description = 'Generate connects';

    public function handle()
    {
        $this->relinking->generate();

        $this->info('Generated');

        return 0;
    }
}
