<?php namespace JasonLewis\Watcher;

use Closure;
use JasonLewis\Watcher\Resource\Resource;

class Tracker {

	/**
	 * Array of tracked resources.
	 * 
	 * @var array
	 */
	protected $tracked = array();

	/**
	 * Register a resource with the tracker.
	 * 
	 * @param  JasonLewis\Watcher\Resource\Resource  $resource
	 * @param  Closure  $callback
	 * @return void
	 */
	public function register(Resource $resource, Closure $callback)
	{
		$this->tracked[$resource->getKey()] = compact('resource', 'callback');
	}

	/**
	 * Determine if a resource is tracked.
	 * 
	 * @param  JasonLewis\Watcher\Resource\Resource  $resource
	 */
	public function isTracked(Resource $resource)
	{
		return isset($this->tracked[$resource->getKey()]);
	}

	/**
	 * Detect any changes on the tracked resources.
	 * 
	 * @return void
	 */
	public function checkTrackings()
	{
		foreach ($this->tracked as $name => $tracked)
		{
			extract($tracked);

			if ( ! $events = $resource->detectChanges())
			{
				return;
			}

			foreach ($events as $event)
			{
				if ($event instanceof Event)
				{
					call_user_func($callback, $event->getResource(), $event);
				}
			}
		}
	}

}