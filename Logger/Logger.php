<?php

namespace Vipps\SignupIntegration\Logger;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    const PATH_LOGGER = __DIR__ . "/vippslogger.txt";
    protected $log;

    public function __construct()
    {
        $this->log = fopen(self::PATH_LOGGER, 'a');
    }

    public function __destruct()
    {
        fclose($this->log);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        fwrite
        (
            $this->log,
            "[{$this->dateTime()}] [".strtoupper($level)."]: $message" . PHP_EOL
        );
    }

    public function dateTime()
    {
        $time = new \DateTime();
        return $time->format('Y-m-d H:i:s');
    }
}