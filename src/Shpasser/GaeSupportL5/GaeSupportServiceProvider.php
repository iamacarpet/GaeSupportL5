<?php

namespace Shpasser\GaeSupportL5;

use Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem as Flysystem;
use Shpasser\GaeSupportL5\Setup\SetupCommand;
use Shpasser\GaeSupportL5\Filesystem\GaeAdapter as GaeFilesystemAdapter;
use Shpasser\GaeSupportL5\Session\DataStoreSessionHandler;

class GaeSupportServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Session::extend('gae', function($app) {
            // Return implementation of SessionHandlerInterface...
            return new DataStoreSessionHandler;
        });
        Storage::extend('gae', function ($app, $config) {
            return new Flysystem(new GaeFilesystemAdapter($config['root']));
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['gae.setup'] = $this->app->share(function ($app) {
            return new SetupCommand;
        });

        $this->commands('gae.setup');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('gae-support');
    }
}
