<?php namespace JasonLewis\Watcher;

use Closure;
use Illuminate\Filesystem\Filesystem;
use JasonLewis\Watcher\Resource\FileResource;
use JasonLewis\Watcher\Resource\DirectoryResource;

class Watcher {

	/**
	 * Tracker instance.
	 * 
	 * @var JasonLewis\Watcher\Tracker
	 */
	protected $tracker;

	/**
	 * Illuminate filesystem instance.
	 * 
	 * @var Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * Indicates if the watcher is watching.
	 * 
	 * @var bool
	 */
	protected $watching = false;

	/**
	 * Create a new watcher instance.
	 * 
	 * @param  JasonLewis\Watcher\Tracker  $tracker
	 * @param  Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(Tracker $tracker, Filesystem $files)
	{
		$this->tracker = $tracker;
		$this->files = $files;
	}

	/**
	 * Register a resource to be watched.
	 * 
	 * @param  string  $resource
	 * @param  Closure  $callback
	 * @return JasonLewis\Watcher\Watcher
	 */
	public function watch($resource, Closure $callback)
	{
		if ($this->files->isDirectory($resource))
		{
			$resource = new DirectoryResource($resource, $this->files);
		}
		else
		{
			$resource = new FileResource($resource, $this->files);
		}

		$this->tracker->register($resource, $callback);

		return $this;
	}

	/**
	 * Start watching for a given interval. The interval and timeout and measured
	 * in microseconds, so 1,000,000 microseconds is equal to 1 second.
	 * 
	 * @param  int  $interval
	 * @param  int  $timeout
	 * @return void
	 */
	public function startWatch($interval = 1000000, $timeout = null)
	{
		$this->watching = true;

		$timeWatching = 0;

		while ($this->watching)
		{
			usleep($interval);

			$timeWatching += $interval;

			if ( ! is_null($timeout) and $timeWatching > $timeout)
			{
				break;
			}

			$this->tracker->checkTrackings();
		}
	}

	/**
	 * Stop watching.
	 * 
	 * @return void
	 */
	public function stopWatch()
	{
		$this->watching = false;
	}

	/**
	 * Determine if watcher is watching.
	 * 
	 * @return bool
	 */
	public function isWatching()
	{
		return $this->watching;
	}

}