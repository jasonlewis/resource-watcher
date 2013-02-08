<?php

use Mockery as m;
use Illuminate\Filesystem\Filesystem;
use JasonLewis\ResourceWatcher\Event;
use JasonLewis\ResourceWatcher\Resource\FileResource;
use JasonLewis\ResourceWatcher\Resource\DirectoryResource;

class DirectoryResourceTest extends PHPUnit_Framework_TestCase {

	public function testDescendantDetection()
	{
		$files = new Filesystem;
		$resource = new DirectoryResource(__DIR__, $files);
		$resource->setupDirectory();
		$this->assertEquals(6, count($resource->getDescendants()));
	}

}