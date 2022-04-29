# gpxcat's laravel_log_formatter

Add the config to your `config/logging.php` file:

```
    'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'tap' => [\Gpxcat\LaravelLogFormatter\Formatter::class],
        ],
```
