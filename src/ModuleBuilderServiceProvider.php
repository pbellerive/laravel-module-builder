<?php

namespace Laravue3\ModuleBuilder;


use Illuminate\Support\ServiceProvider;

class ModuleBuilderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
             __DIR__.'/config.php',
            'moduleBuilder'
        );

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                    ModuleBuilder::class,
                ]);
        }
    }
}
