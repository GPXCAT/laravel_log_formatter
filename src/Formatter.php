<?php
namespace Gpxcat\LaravelLogFormatter;

use Auth;
use Request;

class Formatter
{
    /**
     * Customize the given logger instance.
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(function ($record) {
                $append = self::appendInfo();
                $record['extra'] = $append;

                // 如果是CLI的話直接印出Log
                if (strpos(php_sapi_name(), 'cli') !== false) {
                    $colors = array();
                    $colors['EMERGENCY'] = array("\033[101m", "\033[49m");
                    $colors['ALERT'] = array("\033[43m", "\033[49m");
                    $colors['CRITICAL'] = array("\033[41m", "\033[49m");
                    $colors['ERROR'] = array("\033[31m", "\033[39m");
                    $colors['WARNING'] = array("\033[33m", "\033[39m");
                    $colors['NOTICE'] = array("\033[32m", "\033[39m");
                    $colors['INFO'] = array("\033[36m", "\033[39m");
                    $colors['DEBUG'] = array('', '');

                    // 如果沒有對應的色彩的話就不加
                    if (isset($colors[$record["level_name"]])) {
                        $color = $colors[$record["level_name"]];
                    } else {
                        $color = array('', '');
                    }

                    echo $color[0] . $record["level_name"] . ': ' . $record['message'] . '  ' . json_encode($append) . $color[1] . "\n";
                }
                return $record;
            });
        }
    }

    public static function appendInfo()
    {
        $append = [];
        if (strpos(php_sapi_name(), 'cli') !== false) {
            $append['type'] = 'CLI';
            // 執行序ID
            $append['pid'] = getmypid();
            // 執行指令
            $append['argv'] = $_SERVER['argv'];
        } else {
            $append['type'] = 'WEB';
            // 記錄IP
            $append['ip'] = Request::getClientIP();
            // 記錄呼叫的路徑
            $append['path'] = '/' . Request::path();
            // 記錄登入狀態
            $guards = [];
            collect(config('auth.guards'))->each(function ($value, $key) use (&$guards) {
                $auth = auth()->guard($key)->user() ? Auth::guard($key)->user() : null;
                if ($auth) {
                    $guards[$key] = [$auth->getKeyName() => $auth->{$auth->getKeyName()}];
                    if (isset($value['log_show_column'])) {
                        $guards[$key][$value['log_show_column']] = $auth->{$value['log_show_column']};
                    }
                }
            });
            $append['guards'] = $guards;
        }

        return $append;
    }
}
