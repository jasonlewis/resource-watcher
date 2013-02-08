<?php

use Mockery as m;
use JasonLewis\ResourceWatcher\Event;
use JasonLewis\ResourceWatcher\Listener;

class ListenerTest extends PHPUnit_Framework_TestCase {

	public function testCanRegisterBindings()
	{
		$listener = new Listener;
		$listener->onCreate(function(){});
		$listener->onModify(function(){});
		$listener->onDelete(function(){});
		$this->assertArrayHasKey('modify', $listener->getBindings());
		$this->assertArrayHasKey('create', $listener->getBindings());
		$this->assertArrayHasKey('delete', $listener->getBindings());
		$this->assertTrue($listener->isBound('modify'));
		$this->assertTrue($listener->isBound('create'));
		$this->assertTrue($listener->isBound('delete'));
	}

	public function testCanGetBinding()
	{
		$listener = new Listener;
		$listener->onCreate(function(){
			return 'foo';
		});
		$bindings = $listener->getBindings('create');
		$this->assertEquals('foo', $bindings[0]());
	}

	public function testDetermineEventBinding()
	{
		$resource = m::mock('JasonLewis\ResourceWatcher\Resource\Resource');
		$listener = new Listener;
		$event = new Event($resource, Event::RESOURCE_CREATED);
		$this->assertEquals('create', $listener->determineEventBinding($event));
		$event = new Event($resource, Event::RESOURCE_MODIFIED);
		$this->assertEquals('modify', $listener->determineEventBinding($event));
		$event = new Event($resource, Event::RESOURCE_DELETED);
		$this->assertEquals('delete', $listener->determineEventBinding($event));
	}

}