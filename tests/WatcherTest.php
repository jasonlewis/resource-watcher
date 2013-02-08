<?php

use Mockery as m;
use JasonLewis\ResourceWatcher\Watcher;
use JasonLewis\ResourceWatcher\Tracker;

class WatcherTest extends PHPUnit_Framework_TestCase {

	public function testWatchDirectoryResource()
	{
		$tracker = new Tracker;
		$files = m::mock('Illuminate\Filesystem\Filesystem');
		$files->shouldReceive('exists')->once()->andReturn(true);
		$files->shouldReceive('isDirectory')->twice()->andReturn(true);
		$files->shouldReceive('lastModified')->once()->andReturn(time());
		$watcher = new Watcher($tracker, $files);
		$watcher->watch(__DIR__);
		$tracked = $watcher->getTracker()->getTracked();
		$resource = array_pop($tracked);
		$this->assertInstanceOf('JasonLewis\ResourceWatcher\Resource\DirectoryResource', $resource[0]);
	}

	public function testWatchFileResource()
	{
		$tracker = new Tracker;
		$files = m::mock('Illuminate\Filesystem\Filesystem');
		$files->shouldReceive('exists')->once()->andReturn(true);
		$files->shouldReceive('isDirectory')->once()->andReturn(false);
		$files->shouldReceive('lastModified')->once()->andReturn(time());
		$watcher = new Watcher($tracker, $files);
		$watcher->watch(__DIR__);
		$tracked = $watcher->getTracker()->getTracked();
		$resource = array_pop($tracked);
		$this->assertInstanceOf('JasonLewis\ResourceWatcher\Resource\FileResource', $resource[0]);
	}

	public function testWatchCanBeStarted()
	{
		$tracker = new Tracker;
		$files = m::mock('Illuminate\Filesystem\Filesystem');
		$files->shouldReceive('exists')->once()->andReturn(true);
		$files->shouldReceive('isDirectory')->once()->andReturn(false);
		$files->shouldReceive('lastModified')->once()->andReturn(time());
		$watcher = new Watcher($tracker, $files);
		$watcher->watch(__DIR__);
		$startTime = time();
		$watcher->startWatch(1000000, 1000000);
		$this->assertEquals(1, time() - $startTime);
	}

	public function testCanGetTrackerInstance()
	{
		$tracker = new Tracker;
		$files = m::mock('Illuminate\Filesystem\Filesystem');
		$files->shouldReceive('exists')->once()->andReturn(true);
		$files->shouldReceive('isDirectory')->once()->andReturn(false);
		$files->shouldReceive('lastModified')->once()->andReturn(time());
		$watcher = new Watcher($tracker, $files);
		$this->assertInstanceOf('JasonLewis\ResourceWatcher\Tracker', $watcher->getTracker());	
	}

	public function testTrackingsAreChecked()
	{
		$tracker = new Tracker;
		$files = new Illuminate\Filesystem\Filesystem;
		$watcher = new Watcher($tracker, $files);
		touch(__DIR__.'/mock.file');
		$listener = $watcher->watch(__DIR__.'/mock.file');
		$this->assertInstanceOf('JasonLewis\ResourceWatcher\Listener', $listener);
		$created = $modified = $deleted = false;
		$listener->onCreate(function($resource) use (&$created)
		{
			$created = true;
		});
		$listener->onModify(function($resource) use (&$modified)
		{
			$modified = true;
		});
		$listener->onDelete(function($resource) use (&$deleted)
		{
			$deleted = true;
		});
		$iterations = 0;
		$watcher->startWatch(10000, 30000, function($watcher) use (&$iterations, $files)
		{
			if ($iterations == 0)
			{
				touch(__DIR__.'/mock.file', time() + 3600);
			}
			
			if ($iterations == 1)
			{
				unlink(__DIR__.'/mock.file');
			}
			
			if ($iterations == 2)
			{
				touch(__DIR__.'/mock.file');
			}

			$iterations++;
		});
		unlink(__DIR__.'/mock.file');
		$this->assertTrue($created);
		$this->assertTrue($modified);
		$this->assertTrue($deleted);
	}

}