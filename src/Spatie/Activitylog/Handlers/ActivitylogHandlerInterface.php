<?php

namespace Spatie\Activitylog\Handlers;

interface ActivitylogHandlerInterface
{
    /**
     * Log some activity.
     *
     * @param string $text
     * @param string $level
     * @param string $user
     * @param array  $attributes
     *
     * @return bool
     */
    public function log($text, $level = 'info', $user = '', $attributes = []);

    /**
     * Clean old log records.
     *
     * @param int $maxAgeInMonths
     *
     * @return bool
     */
    public function cleanLog($maxAgeInMonths);
}
