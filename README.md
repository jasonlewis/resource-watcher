# Resource Watcher

A resource watcher allows you to watch a resource for any changes. This means you can watch a directory and then listen for any changes to files within that directory or to the directory itself.

## Installation

You can use the Resource Watcher in any application, however it's bundled with a service provider for ease of use within Laravel 4. Add Resource Watcher as a dependency in your `composer.json` file.

```
"jasonlewis/resource-watcher": "1.0.*"
```

## Usage

The Resource Watcher is best used from a console. To use it from your console you need a command to run. In Laravel 4 a command can be created that uses Resource Watcher binding. `$app['watcher']` is bound to the application in `JasonLewis\ResourceWatcher\ResourceWatcherServiceProvider`. Remember to add the service provider to the array of service providers in `app/config/app.php`.

If you're not using Laravel 4 an example command can be found in the `watcher` file. Once you've customized the command you can run it from your console.

```
$ php watcher
```

Any changes you make to the resource will be outputted to the console.

### Watching Resources

To watch a resource you first need an instance of `JasonLewis\ResourceWatcher\ResourceWatcher`. If you're using Laravel 4 you can watch resources as shown.

```php
$listener = $app['watcher']->watch('path/to/resource');

// Or if you don't have an instance of the application container.

$listener = App::make('watcher')->watch('path/to/resource');
```

If you're not using Laravel 4 you need to create an instance manually.

```php
$files = new Illuminate\Filesystem\Filesystem;
$tracker = new JasonLewis\ResourceWatcher\Tracker;

$watcher = new JasonLewis\ResourceWatcher\ResourceWatcher($tracker, $files);

$listener = $watcher->watch('path/to/resource');
```

You can watch as many resources as you like.

### Listening For Changes

When you watch a resource an instance of `JasonLewis\ResourceWatcher\Listener` is returned. This allows you to listen for changes to each resource that's being watched.

There are three changes you can listen for: `onModify`, `onCreate`, and `onDelete`.

```php
$listener->onModify(function($resource)
{
    echo "{$resource->getPath()} has been modified.".PHP_EOL;
});
```

> Remember that each resource that's watched will return an instance of `JasonLewis\ResourceWatcher\Listener`, so be sure you attach listeners to the correct one!

### Starting The Watch

Once you're watching some resources and have any listeners setup you can start the watching process.

```php
// If you're using Laravel 4.
$app['watcher']->startWatch();

// Or if you have an instance of JasonLewis\ResourceWatcher\ResourceWatcher
$watcher->startWatch();
```

## License

Resource Watcher is released under the 2-clause BSD license. See the `LICENSE` for more details.