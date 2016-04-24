<?php

namespace Spatie\Activitylog\Handlers;

use Log;

class DefaultLaravelHandler implements ActivitylogHandlerInterface
{
    /**
     * Log activity in Laravels log handler.
     *
     * @param string $text
     * @param string $level
     * @param string $userId
     * @param array  $attributes
     *
     * @return bool
     */
    public function log($text, $level = 'info', $userId = '', $attributes = [])
    {
        $logText = $text;
        $logText .= ($userId != '' ? ' (by user_id ' . $userId . ')' : '');
        $logText .= (count($attributes)) ? PHP_EOL . print_r($attributes, true) : '';

        if (method_exists('Log', $level)) {
            forward_static_call(['Log', $level], $logText);
        } else {
            Log::info($logText);
        }

        return true;
    }

    /**
     * Clean old log records.
     *
     * @param int $maxAgeInMonths
     *
     * @return bool
     */
    public function cleanLog($maxAgeInMonths)
    {
        //this handler can't clean it's records

        return true;
    }
}
