<?php namespace JasonLewis\ResourceWatcher\Resource;

use JasonLewis\ResourceWatcher\Event;
use Illuminate\Filesystem\Filesystem;

class Resource {

	/**
	 * Resource string.
	 * 
	 * @var string
	 */
	protected $resource;

	/**
	 * Illuminate filesystem instance.
	 * 
	 * @var Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * Resources last modified timestamp.
	 * 
	 * @var int
	 */
	protected $lastModified;

	/**
	 * Indicates whether the resource exists or not.
	 * 
	 * @var bool
	 */
	protected $exists = true;

	/**
	 * Create a new resource instance.
	 * 
	 * @param  string  $resource
	 * @param  Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct($resource, Filesystem $files)
	{
		$this->resource = $resource;
		$this->files = $files;
		$this->exists = $this->files->exists($resource);
		$this->lastModified = ! $this->exists ?: $this->files->lastModified($resource);
	}

	/**
	 * Detect any changes to the resource.
	 * 
	 * @return array
	 */
	public function detectChanges()
	{
		clearstatcache(true, $this->resource);

		if ( ! $this->exists and $this->files->exists($this->resource))
		{
			$this->lastModified = $this->files->lastModified($this->resource);
			$this->exists = true;

			return array(new Event($this, Event::RESOURCE_CREATED));
		}
		elseif ($this->exists and ! $this->files->exists($this->resource))
		{
			$this->exists = false;

			return array(new Event($this, Event::RESOURCE_DELETED));
		}
		elseif ($this->exists and $this->isModified())
		{
			$this->lastModified = $this->files->lastModified($this->resource);

			return array(new Event($this, Event::RESOURCE_MODIFIED));
		}

		return array();
	}

	/**
	 * Determine if the resource has been modified.
	 * 
	 * @return bool
	 */
	public function isModified()
	{
		return $this->lastModified < $this->files->lastModified($this->resource);
	}

	/**
	 * Get the resource key.
	 * 
	 * @return string
	 */
	public function getKey()
	{
		return md5($this->resource);
	}

	/**
	 * Get the resource path.
	 * 
	 * @return string
	 */
	public function getPath()
	{
		return $this->resource;
	}

	/**
	 * Get the resources last modified timestamp.
	 * 
	 * @return int
	 */
	public function getLastModified()
	{
		return $this->lastModified;
	}
	
}