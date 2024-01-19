<?php

namespace Gpxcat\LaravelLogFormatter;

use Gelf\Publisher;
use Gelf\Transport\IgnoreErrorTransportWrapper;
use Gelf\Transport\UdpTransport;
use Gpxcat\LaravelLogFormatter\GelfHandler;
use Monolog\Logger;

class GraylogLogger
{
    const GRAYLOG_CHANNEL = 'graylog';

    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config): Logger
    {
        $transport = new IgnoreErrorTransportWrapper(
            new UdpTransport($config['host'], $config['port'])
        );

        $publisher = new Publisher($transport);
        $handler = new GelfHandler($publisher, $config['level']);

        return new Logger(self::GRAYLOG_CHANNEL, [$handler]);
    }
}
