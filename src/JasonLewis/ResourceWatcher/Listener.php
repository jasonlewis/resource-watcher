<?php namespace JasonLewis\ResourceWatcher;

use Closure;
use RuntimeException;
use JasonLewis\ResourceWatcher\Resource\Resource;

class Listener {

	/**
	 * Listener bindings array.
	 *
	 * @var array
	 */
	protected $bindings = array();

	/**
	 * Bind a listener to a given event.
	 *
	 * @param  string  $event
	 * @param  Closure  $callback
	 * @return void
	 */
	public function on($event, Closure $callback)
	{
		if ( ! in_array($event, array('modify', 'delete', 'create')))
		{
			throw new RuntimeException('Could not bind to unknown event '.$event);
		}

		$this->registerBinding($event, $callback);
	}

	/**
	 * Bind to a modify event.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function onModify(Closure $callback)
	{
		$this->registerBinding('modify', $callback);
	}

	/**
	 * Alias of the onModify method.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function modify(Closure $callback)
	{
		$this->onModify($callback);
	}

	/**
	 * Bind to a delete event.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function onDelete(Closure $callback)
	{
		$this->registerBinding('delete', $callback);
	}

	/**
	 * Alias of the onDelete method.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function delete(Closure $callback)
	{
		$this->onDelete($callback);
	}

	/**
	 * Bind to a create event.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function onCreate(Closure $callback)
	{
		$this->registerBinding('create', $callback);
	}

	/**
	 * Alias of the onCreate method.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function create(Closure $callback)
	{
		$this->onCreate($callback);
	}

	/**
	 * Register a binding.
	 *
	 * @param  string  $binding
	 * @param  Closure  $callback
	 * @return void
	 */
	protected function registerBinding($binding, Closure $callback)
	{
		$this->bindings[$binding][] = $callback;
	}

	/**
	 * Determine if a binding is bound to the listener.
	 *
	 * @param  string  $binding
	 * @return bool
	 */
	public function isBound($binding)
	{
		return isset($this->bindings[$binding]);
	}

	/**
	 * Get the bindings or a specific array of bindings.
	 *
	 * @param  string  $binding
	 * @return array
	 */
	public function getBindings($binding = null)
	{
		if (is_null($binding))
		{
			return $this->bindings;
		}

		return $this->bindings[$binding];
	}

	/**
	 * Determine the binding for a given event.
	 *
	 * @param  JasonLewis\ResourceWatcher\Event  $event
	 * @return string
	 */
	public function determineEventBinding(Event $event)
	{
		switch ($event->getCode())
		{
			case Event::RESOURCE_DELETED:
				return 'delete';
				break;
			case Event::RESOURCE_CREATED:
				return 'create';
				break;
			case Event::RESOURCE_MODIFIED:
				return 'modify';
			break;
		}
	}

}