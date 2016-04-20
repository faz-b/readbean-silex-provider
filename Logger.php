<?php

namespace Fazb\RedBean;

use Psr\Log\LoggerInterface;
use Monolog\Logger as Monolog;
use RedBeanPHP\Logger as BaseLogger;
use RedBeanPHP\Logger\RDefault\Debug;
use RedBeanPHP\RedException;

/**
* Logger
*/
class Logger extends Debug implements BaseLogger
{
    protected $logger;

    protected $debugQuery;

    protected $strLen = 40;

    protected $throwException = false;

    public function __construct(LoggerInterface $logger, $throwException = true, $debugQuery = false)
    {
        $this->logger         = $logger;
        $this->throwException = $throwException;
        $this->debugQuery     = $debugQuery;
        $this->mode           = self::C_LOGGER_ARRAY;
    }

    /**
     * Writes a query for logging with all bindings / params filled
     * in.
     *
     * @param string $newSql   the query
     * @param array  $bindings the bindings to process (key-value pairs)
     *
     * @return string
     */
    private function writeQuery($newSql, $newBindings)
    {
        //avoid str_replace collisions: slot1 and slot10 (issue 407).
        uksort($newBindings, function ($a, $b) {
            return (strlen($b) - strlen($a));
        });

        $newStr = $newSql;
        foreach ($newBindings as $slot => $value) {
            if (strpos($slot, ':') === 0) {
                $newStr = str_replace($slot, $this->fillInValue($value), $newStr);
            }
        }
        return $newStr;
    }

    public function log()
    {
        if (func_num_args() < 1) {
            return;
        };

        $message = '';
        if ($this->debugQuery) {
            $sql = func_get_arg(0);

            if (func_num_args() < 2) {
                $bindings = array();
            } else {
                $bindings = func_get_arg(1);
            }

            if (is_array($bindings)) {
                $newSql      = $this->normalizeSlots($sql);
                $newBindings = $this->normalizeBindings($bindings);
                $message     = $this->writeQuery($newSql, $newBindings);
            } else {
                $message = $sql;
            }

            if ($message) {
                $this->doLog($message);
            }
        } else {
            foreach (func_get_args() as $argument) {
                if (is_array($argument)) {
                    $argument = array_filter($argument);
                    $message  = print_r($argument, true);
                    $this->doLog($message);
                } else {
                    $this->doLog($argument);
                }
            }
        }
    }

    protected function doLog($message)
    {
        $level = preg_match('@error@i', $message) ? Monolog::CRITICAL : Monolog::DEBUG;
        $this->logger->addRecord($level, $message);

        if ($level === Monolog::CRITICAL && $this->throwException) {
            throw new RedException($message);
        }
    }
}
