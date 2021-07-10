<?php

namespace MasterDmx\LaravelRelinking\ConsoleCommands;

use Illuminate\Console\Command as BaseCommand;
use MasterDmx\LaravelRelinking\Relinking;

abstract class Command extends BaseCommand
{
    protected Relinking $relinking;

    public function __construct(Relinking $relinking)
    {
        $this->relinking = $relinking;

        parent::__construct();
    }
}
