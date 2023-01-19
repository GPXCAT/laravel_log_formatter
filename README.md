# gpxcat's laravel_log_formatter

Add the `.env` file:
```
LOG_CHANNEL=stack
GRAYLOG_URL=10.1.6.38
GRAYLOG_PORT=12201
IFCONFIG_URL=http://10.1.1.188:8080/ip
```

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

```
'graylog' => [
    'driver' => 'monolog',
    'handler' => \Monolog\Handler\GelfHandler::class,
    'handler_with' => [
        'publisher' => app(\Gpxcat\LaravelLogFormatter\GraylogSetup::class)->getGelfPublisher(),
    ],
    'formatter' => \Gpxcat\LaravelLogFormatter\GelfFormatter::class,
],
```
