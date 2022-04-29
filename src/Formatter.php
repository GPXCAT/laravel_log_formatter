<?php
namespace Gpxcat\LaravelLogFormatter;

use Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Authenticated;

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
                if (strpos(php_sapi_name(), 'cli') !== false) {
                    $append[] = 'CLI';
                    // 執行序ID
                    $append[] = getmypid();
                    // 執行指令
                    $append[] = implode(' ', $_SERVER['argv']);
                } else {
                    $append[] = 'WEB';
                    // 記錄IP
                    $append[] = \Request::getClientIP();
                    // 記錄呼叫的路徑
                    $append[] = '/' . \Request::path();

                    dd(auth());

                    // dd(
                    //     session_status(),
                    //     PHP_SESSION_DISABLED,
                    //     PHP_SESSION_NONE,
                    //     PHP_SESSION_ACTIVE
                    // );
                    if (session_status() !== PHP_SESSION_DISABLED) {
                        // 記錄登入狀態
                        $append[] = Auth::user() ? Auth::user()->code : '[EMP-NOLOGIN]';
                        // 記錄登入狀態
                        // $append[] = Auth::guard('mobile_web')->user() ? Auth::guard('mobile_web')->user()->code : '[MEM-NOLOGIN]';
                    }
                }
                $appendStr = implode(' - ', $append);

                $record['message'] = $appendStr . ' - ' . $record['message'];

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

                    $record['message'] = $this->ToLogStr($record['message']);
                    echo $color[0] . $record["level_name"] . ': ' . $record['message'] . $color[1] . "\n";
                }
                return $record;
            });
        }
    }

    private function ToLogStr($input)
    {
        return trim(preg_replace('/\s+/', ' ', preg_replace('/[\r\n\t\f\b]/', ' ', $input)));
    }
}