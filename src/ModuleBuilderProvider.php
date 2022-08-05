<?php

namespace ModuleBuilder;

use Illuminate\Support\ServiceProvider;

class ModuleBuilderProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
             __DIR__.'/../ModuleBuilder/config.php',
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
        //
    }
}
