<?php

namespace Spatie\Activitylog;

interface LogsActivityInterface
{
    /**
     * Get the message that needs to be logged for the given event.
     * If an array is returned the first element of the array is taken as the message and the second as the log level.
     *
     * @param string $eventName
     *
     * @return string|array
     */
    public function getActivityDescriptionForEvent($eventName);
}
