<?php namespace JasonLewis\ResourceWatcher\Resource;

use DirectoryIterator;
use JasonLewis\ResourceWatcher\Event;
use Illuminate\Filesystem\Filesystem;

class DirectoryResource extends Resource {

	/**
	 * Array of directory resources children.
	 * 
	 * @var array
	 */
	protected $children = array();

	/**
	 * Create a new directory resource instance.
	 * 
	 * @param  string  $resource
	 * @param  Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct($resource, Filesystem $files)
	{
		parent::__construct($resource, $files);

		$this->children = $this->detectDirectoryChildren();
	}

	/**
	 * Detect any changes to the resource.
	 * 
	 * @return array
	 */
	public function detectChanges()
	{
		$events = parent::detectChanges();

		// If the parent directories event is a modified code then we'll remove it so we don't
		// get a double up of modified events when a child file or directory is created or
		// deleted.
		if ($events and $events[0]->getCode() == Event::RESOURCE_MODIFIED)
		{
			$events = array();
		}

		foreach ($this->children as $key => $child)
		{
			$childEvents = $child->detectChanges();

			foreach ($childEvents as $childEvent)
			{
				if ($childEvent instanceof Event and $childEvent->getCode() == Event::RESOURCE_DELETED)
				{
					unset($this->children[$key]);
				}
			}

			$events = array_merge($events, $childEvents);
		}

		// If this directory still exists we'll check the directory children again for any
		// new children. We'll then create a created event.
		if ($this->exists)
		{
			foreach ($this->detectDirectoryChildren() as $key => $child)
			{
				if ( ! isset($this->children[$key]))
				{
					$this->children[$key] = $child;

					$events[] = new Event($child, Event::RESOURCE_CREATED);
				}
			}
		}

		return $events;
	}

	/**
	 * Detect the directory resources children resources.
	 * 
	 * @return array
	 */
	protected function detectDirectoryChildren()
	{
		$children = array();

		foreach (new DirectoryIterator($this->resource) as $file)
		{
			if ($file->isDir() and ! $file->isDot())
			{
				$resource = new DirectoryResource($file->getRealPath(), $this->files);

				$children[$resource->getKey()] = $resource;
			}
			elseif ($file->isFile())
			{
				$resource = new FileResource($file->getRealPath(), $this->files);

				$children[$resource->getKey()] = $resource;
			}
		}

		return $children;
	}

	/**
	 * Get the directory resources children.
	 * 
	 * @return array
	 */
	public function getChildren()
	{
		return $this->children;
	}

}