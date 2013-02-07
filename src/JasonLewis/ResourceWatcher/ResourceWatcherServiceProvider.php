<?php namespace JasonLewis\ResourceWatcher;

use Illuminate\Support\ServiceProvider;

class ResourceWatcherServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('jasonlewis/watcher');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['watcher'] = $this->app->share(function($app)
		{
			$tracker = new Tracker;

			return new ResourceWatcher($tracker, $app['files']);
		});

		$this->registerWatchCommand();
	}

	/**
	 * Register the watch command.
	 * 
	 * @return void
	 */
	protected function registerWatchCommand()
	{
		$this->app['commands.watch'] = $this->app->share(function($app)
		{
			return new WatchCommand($app['watcher']);
		});

		$this->commands('commands.watch');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('watcher', 'commmands.watch');
	}

}