<?php

namespace Sakadigital\ApiDocumentation;

use Illuminate\Support\ServiceProvider;

class ApiDocumentationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //publish config file
        $this->publishes([
            __DIR__.'/config/apidoc.php' => config_path('apidoc.php'),
        ], 'config');

        //publish asset
        $this->publishes([
            __DIR__.'/public' => public_path('apidoc'),
        ], 'public');

        //register location views
        $this->loadViewsFrom(__DIR__.'/views', 'doc');

        //register route
        if ( ! $this->app->routesAreCached()) {
            require __DIR__.'/routes.php';
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
