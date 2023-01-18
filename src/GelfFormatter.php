<?php
namespace Gpxcat\LaravelLogFormatter;

use \Gelf\Message;
use \Monolog\Formatter\GelfMessageFormatter;

class GelfFormatter extends GelfMessageFormatter
{

    /**
     * {@inheritdoc}
     */
    public function format(array $record): Message
    {
        $append[] = env('APP_NAME');
        $append[] = getHostName();
        $append = array_merge($append, Formatter::appendInfo());
        $appendStr = implode(' - ', $append);
        $record['message'] = $appendStr . ' - ' . $record['message'];

        return parent::format($record);
    }
}
