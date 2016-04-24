<?php

namespace Spatie\Activitylog;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Auth\Guard;
use Spatie\Activitylog\Handlers\ActivitylogHandlerInterface;
use Spatie\Activitylog\Handlers\BeforeHandler;
use Spatie\Activitylog\Handlers\BeforeHandlerInterface;
use Spatie\Activitylog\Handlers\DefaultLaravelHandler;
use Request;
use Config;

class ActivitylogSupervisor
{

    const LEVEL_EMERGENCY = 'emergency';
    const LEVEL_ALERT = 'alert';
    const LEVEL_CRITICAL = 'critical';
    const LEVEL_ERROR = 'error';
    const LEVEL_WARNING = 'warning';
    const LEVEL_NOTICE = 'notice';
    const LEVEL_INFO = 'info';
    const LEVEL_DEBUG = 'debug';

    /**
     * @var ActivitylogHandlerInterface[] logHandlers
     */
    protected $logHandlers = [];

    protected $auth;

    protected $config;

    /**
     * Create the logsupervisor using a default Handler
     * Also register Laravels Log Handler if needed.
     *
     * @param Handlers\ActivitylogHandlerInterface $logHandler
     * @param Repository                           $config
     * @param Guard                                $auth
     */
    public function __construct(Handlers\ActivitylogHandlerInterface $logHandler, Repository $config, Guard $auth)
    {
        $this->config = $config;

        $this->logHandlers[] = $logHandler;

        if ($this->config->get('activitylog.alsoLogInDefaultLog')) {
            $this->logHandlers[] = new DefaultLaravelHandler();
        }

        $this->auth = $auth;
    }

    /**
     * Log some activity to all registered log handlers.
     *
     * @param        $text
     * @param string $level
     * @param string $userId
     *
     * @return bool
     */
    public function log($text, $level = self::LEVEL_INFO, $userId = '')
    {
        $userId = $this->normalizeUserId($userId);

        if (!$this->shouldLogCall($text, $level, $userId)) {
            return false;
        }

        $ipAddress = Request::getClientIp();

        foreach ($this->logHandlers as $logHandler) {
            $logHandler->log($text, $level, $userId, compact('ipAddress'));
        }

        return true;
    }

    /**
     * Clean out old entries in the log.
     *
     * @return bool
     */
    public function cleanLog()
    {
        foreach ($this->logHandlers as $logHandler) {
            $logHandler->cleanLog(Config::get('activitylog.deleteRecordsOlderThanMonths'));
        }

        return true;
    }

    /**
     * Normalize the user id.
     *
     * @param object|int $userId
     *
     * @return int
     */
    public function normalizeUserId($userId)
    {
        if (is_numeric($userId)) {
            return $userId;
        }

        if (is_object($userId)) {
            return $userId->id;
        }

        if ($this->auth->check()) {
            return $this->auth->user()->id;
        }

        if (is_numeric($this->config->get('activitylog.defaultUserId'))) {
            return $this->config->get('activitylog.defaultUserId');
        };

        return '';
    }

    /**
     * Determine if this call should be logged.
     *
     * @param        $text
     * @param string $level
     * @param        $userId
     *
     * @return bool
     */
    protected function shouldLogCall($text, $level, $userId)
    {
        $beforeHandler = $this->config->get('activitylog.beforeHandler');

        if (is_null($beforeHandler) || $beforeHandler == '') {
            return true;
        }

        /** @var BeforeHandlerInterface $handler */
        $handler = app($beforeHandler);
        return $handler->shouldLog($text, $level, $userId);
    }
}
