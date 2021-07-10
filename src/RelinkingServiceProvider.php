<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Support\ServiceProvider;
use MasterDmx\LaravelRelinking\ConsoleCommands\RelinkingGenerate;
use MasterDmx\LaravelRelinking\ConsoleCommands\RelinkingReset;

class RelinkingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([ __DIR__.'/../config/relinking.php' => config_path('relinking.php')], 'config');
        $this->publishes([ __DIR__.'/../migrations' => database_path('migrations')], 'migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                RelinkingGenerate::class,
                RelinkingReset::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom( __DIR__.'/../config/relinking.php', 'relinking');

        LinkableRegistry::fromArray(config('relinking.models', []));
    }
}
