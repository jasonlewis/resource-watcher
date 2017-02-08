<?php

namespace JasonLewis\ResourceWatcher\Integration;

use Illuminate\Support\Facades\Facade;

class Watcher extends Facade {

	protected static function getFacadeAccessor() { return 'watcher'; }

}
