<?php

namespace Gpxcat\LaravelLogFormatter;

use Gelf\Publisher;
use Gelf\Transport\UdpTransport;

class GraylogSetup
{
    public function getGelfPublisher(): Publisher
    {
        $transport = new UdpTransport(env('GRAYLOG_URL'), env('GRAYLOG_PORT'), UdpTransport::CHUNK_SIZE_LAN);
        $publisher = new Publisher();
        $publisher->addTransport($transport);
        return $publisher;
    }
}
