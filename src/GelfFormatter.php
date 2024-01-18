<?php
namespace Gpxcat\LaravelLogFormatter;

use ErrorException;
use Gelf\Message;
use Monolog\Formatter\GelfMessageFormatter;

class GelfFormatter extends GelfMessageFormatter
{

    /**
     * {@inheritdoc}
     */
    public function format($record): Message
    {
        $append['app_name'] = env('APP_NAME');
        $append['server_ip'] = $this->getServerIp();
        $append = array_merge($append, Formatter::appendInfo());
        $record['extra'] = $append;
        return parent::format($record);
    }

    public function getServerIp()
    {
        $filePath = storage_path('logs/myServerIp.txt');

        try {
            $ip = file_get_contents($filePath);
            if (!empty($ip)) {
                $file_creation_date = filectime($filePath);
                if ($file_creation_date < (time() - 86400)) {
                    unlink($filePath);
                }
                return $ip;
            }
        } catch (ErrorException $e) {
            // continue;
        }

        $list = [
            env('IFCONFIG_URL', ''),
            'http://169.254.169.254/latest/meta-data/local-ipv4', # EC2
            'https://ifconfig.io/ip', # Public
        ];

        foreach ($list as $item) {
            if (empty($item)) {continue;}

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $item,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));
            $ip = trim(curl_exec($curl));
            curl_close($curl);

            if (!empty($ip)) {
                file_put_contents($filePath, $ip);
                break;
            }
        }

        return $ip;
    }
}
