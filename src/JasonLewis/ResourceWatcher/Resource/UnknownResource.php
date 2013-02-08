<?php namespace JasonLewis\ResourceWatcher\Resource;

class UnknownResource extends DirectoryResource {

	/**
	 * Detect the directory resources children resources.
	 * 
	 * @return array
	 */
	protected function detectDirectoryChildren()
	{
		if ( ! $this->files->isDirectory($this->resource))
		{
			return array();
		}

		return parent::detectDirectoryChildren();
	}

}