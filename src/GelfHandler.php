<?php

namespace Gpxcat\LaravelLogFormatter;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\GelfHandler as Handler;

class GelfHandler extends Handler
{
    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new \Gpxcat\LaravelLogFormatter\GelfFormatter();
    }
}
