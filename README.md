# gpxcat's laravel_log_formatter

Add the `.env` file:
```
LOG_CHANNEL=stack
GRAYLOG_URL=10.1.6.38
GRAYLOG_PORT=12201
IFCONFIG_URL=http://10.1.1.188:8080/ip
```

Add the config to your `config/logging.php` file:


```php
'stack' => [
    'driver' => 'stack',
    'channels' => ['daily', 'graylog'],
    'ignore_exceptions' => true,
    // ...
],
```

```php
'daily' => [
    'driver' => 'daily',
    // ...
    'tap' => [\Gpxcat\LaravelLogFormatter\Formatter::class],
    // ...
],
```

```php
'graylog' => [
    'driver' => 'custom',
    'via' => \Gpxcat\LaravelLogFormatter\GraylogLogger::class,
    'host' => env('GRAYLOG_URL', ''),
    'port' => env('GRAYLOG_PORT', ''),
    'level' => env('LOG_LEVEL', 'debug'),
],
```
