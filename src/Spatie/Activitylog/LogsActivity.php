<?php

namespace Spatie\Activitylog;

use Activity;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        foreach (static::getRecordActivityEvents() as $eventName) {
            static::$eventName(function (LogsActivityInterface $model) use ($eventName) {

                $message = $model->getActivityDescriptionForEvent($eventName);

                if (is_array($message) && count($message) == 2) {
                    Activity::log($message[0], $message[1]);
                } elseif (!is_array($message) && $message != '') {
                    Activity::log($message);
                }
            });
        }
    }

    /**
     * Set the default events to be recorded if the $recordEvents
     * property does not exist on the model.
     *
     * @return array
     */
    protected static function getRecordActivityEvents()
    {
        if (isset(static::$recordEvents)) {
            return static::$recordEvents;
        }

        return [
            'created', 'updated', 'deleting', 'deleted',
        ];
    }
}
