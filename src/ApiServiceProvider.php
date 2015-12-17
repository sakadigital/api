<?php

namespace Sakadigital\Api;

use URL;
use Route;
use File;
use Illuminate\Http\Request;
use Sakadigital\Api\Documentation;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Request $request, Kernel $kernel)
    {
        $config = $this->app['config'];
        
        if ($config->get('api.bypass_scrf_token'))
        {
            $kernel->pushMiddleware('Sakadigital\Api\Middleware\Api');
        }

        //publish asset
        $this->publishes([
            __DIR__.'/../app' => app_path('Api'),
        ], 'APP');

        //publish config file
        $this->publishes([
            __DIR__.'/../config/api.php' => config_path('api.php'),
        ], 'config');

        //publish asset
        $this->publishes([
            __DIR__.'/../public' => public_path('api'),
        ], 'public');
        
        if ($this->app->runningInConsole()) {
            $config->set('api.enabled', false);
        }

        //stop process if setting is enabled
        if ( ! $config->get('api.enabled')) return;

        if ( ! $config->get('api.version'))
        {
            if ($request->segment(1) === $config->get('api.prefix'))
            {
                //set route config
                $routeConfig = [
                    'namespace' => $config->get('api.namespace').'\Controllers',
                    'prefix' => $config->get('api.prefix')
                ];

                $this->app['router']->group($routeConfig, function() use($config) {
                    $namespace = $config->get('api.namespace');
                    $segment = explode('\\', $namespace);

                    unset($segment[0]);

                    if (count($segment) > 0)
                    {
                        require app_path(implode('/', $segment).'/routes.php');
                    }
                });

                if ($config->get('api.documentation') AND $request->segment(2) === $config->get('api.documentation_prefix'))
                {
                    //register location views
                    $this->loadViewsFrom(__DIR__.'/views', 'doc');
                    
                    Route::get($config->get('api.prefix').'/'.$config->get('api.documentation_prefix').'/{controller?}/{function?}', function($controller='', $function=''){
                        $doc = new Documentation;
                        $data['menu'] = $doc->createMenu($controller, $function);
                        $data['content'] = $doc->createContent($controller, $function);
                        $data['baseUrl'] = URL::to($doc->current_api);

                        return View('doc::view')->with($data);
                    });
                }
            }
        }
        else
        {
            //excep only array type
            if ( ! is_array($config->get('api.version'))) return;

            foreach ($config->get('api.version') as $key => $value)
            {
                //stop process if setting is enabled
                if ( ! $config->get('api.version.'.$key.'.enabled')) continue;

                if ($request->segment(1) === $config->get('api.prefix') AND $request->segment(2) === $config->get('api.version.'.$key.'.prefix'))
                {
                    //set route config
                    $routeConfig = [
                        'namespace' => $config->get('api.version.'.$key.'.namespace').'\Controllers',
                        'prefix' => $config->get('api.prefix').'/'.$config->get('api.version.'.$key.'.prefix')
                    ];

                    $this->app['router']->group($routeConfig, function() use($config, $key) {
                        $namespace = $config->get('api.version.'.$key.'.namespace');
                        $segment = explode('\\', $namespace);

                        unset($segment[0]);

                        if (count($segment) > 0)
                        {
                            if (File::exists(app_path(implode('/', $segment).'/routes.php')))
                            {
                                require app_path(implode('/', $segment).'/routes.php');
                            }
                        }
                    });

                    if ($config->get('api.documentation') AND $request->segment(3) === $config->get('api.documentation_prefix'))
                    {
                        //register location views
                        $this->loadViewsFrom(__DIR__.'/views', 'doc');
                        
                        Route::get($config->get('api.prefix').'/'.$key.'/'.$config->get('api.documentation_prefix').'/{controller?}/{function?}', function($controller='', $function=''){
                            $doc = new Documentation;
                            $data['menu'] = $doc->createMenu($controller, $function);
                            $data['content'] = $doc->createContent($controller, $function);
                            $data['baseUrl'] = URL::to($doc->current_api);
                            $data['state'] = ['controller'=>$controller,'function'=>$function];

                            return View('doc::view')->with($data);
                        });
                    }
                }
            }
        }

        $response = $kernel->handle(
            $request = \Illuminate\Http\Request::capture()
        );
    }
}
