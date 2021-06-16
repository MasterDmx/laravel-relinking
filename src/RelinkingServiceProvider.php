<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Support\ServiceProvider;
use MasterDmx\LaravelRelinking\ContextManager;

class RelinkingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([ __DIR__.'/../config/relinking.php' => config_path('relinking.php')], 'config');
        $this->publishes([ __DIR__.'/../migrations' => database_path('migrations')], 'migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom( __DIR__.'/../config/relinking.php', 'relinking');

        $contexts = new ContextManager();
        $contexts->addFromArray(config('relinking.contexts'));

        // Синглтон реестра контекстов
        $this->app->singleton(ContextManager::class, fn () => $contexts);

        // Синглтон менеджера
        $this->app->singleton(RelinkingManager::class);
    }
}
