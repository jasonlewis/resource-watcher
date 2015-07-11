<?php

namespace JasonLewis\ResourceWatcher\Integration;

use Illuminate\Support\ServiceProvider;
use JasonLewis\ResourceWatcher\Tracker;
use JasonLewis\ResourceWatcher\Watcher;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    	$this->app->singleton('watcher', function ($app) {
            return new Watcher(new Tracker, $app['files']);
        });

        $this->app->alias('watcher', 'JasonLewis\ResourceWatcher\Watcher');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['watcher'];
    }
}
