# Resource Watcher

A resource watcher allows you to watch a resource for any changes. This means you can watch a directory and then listen for any changes to files within that directory or to the directory itself.

## Installation

To install Resource Watcher add it to the `requires` key of your `composer.json` file.

```
"jasonlewis/resource-watcher": "1.0.*"
```

## Usage

The Resource Watcher is best used from a console. An example command can be found in the `watcher` file. Once you've customized the command you can call it from your console.

```
$ php watcher
```

Any changes you make to the resource will be outputted to the console.

### Watching Resources

To watch resources you first need an instance of `JasonLewis\ResourceWatcher\Watcher`. This class has a few dependencies (`JasonLewis\ResourceWatcher\Tracker` and `Illuminate\Filesystem\Filesystem`) that must also be instantiated.

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

> Remember that each call to `$watcher->watch()` will return an instance of `JasonLewis\ResourceWatcher\Listener`, so be sure you attach listeners to the right one!

### Starting The Watch

Once you're watching some resources and have any listeners setup you can start the watching process.

```php
$watcher->startWatch();
```

### Framework Integration

#### Laravel 4

Resource Watcher includes a service provider for the Laravel 4 framework. This service provider will bind an instance of `JasonLewis\ResourceWatcher\Watcher` to the application container under the `watcher` key.

```php
$listener = $app['watcher']->watch('path/to/resource');

// Or if you don't have access to an instance of the application container.
$listener = App::make('watcher')->watch('path/to/resource');
```

Register `JasonLewis\ResourceWatcher\Integration\LaravelServiceProvider` in the array of providers in `app/config/app.php`.

## License

Resource Watcher is released under the 2-clause BSD license. See the `LICENSE` for more details.
