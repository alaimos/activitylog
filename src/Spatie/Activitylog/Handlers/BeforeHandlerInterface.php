<?php

namespace Spatie\Activitylog\Handlers;

interface BeforeHandlerInterface
{
    /**
     * Call to the log will only be made if this function returns true.
     *
     * @param string   $text   The text that will be logged
     * @param string   $level  
     * @param int|null $userId The id of the user that is  associated with the log call
     *
     * @return bool
     */
    public function shouldLog($text, $level = 'info', $userId);
}
