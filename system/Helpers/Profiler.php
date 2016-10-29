<?php
/*
 * Profiler Helper - generate a Profiler information
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Nova\Helpers;

use Nova\Config\Config;
use Nova\Helpers\Number;
use Nova\Support\Facades\DB;
use Nova\Support\Facades\Request;


class Profiler
{
    /**
     * Array holding the configuration.
     */
    protected $config = array();


    protected function __construct()
    {
        $this->config = Config::get('profiler', array());
    }

    protected function getReport()
    {
        $withDatabase = $this->withDatabase();

        // Calculate the variables.
        $memoryUsage = Number::humanSize(memory_get_usage());

        $elapsedTime = $this->getElapsedTime();

        $elapsedStr = sprintf("%01.4f", $elapsedTime);

        $umax = sprintf("%0d", intval(25 / $elapsedTime));

        if ($withDatabase) {
            $queries = $this->getSqlQueries();

            $result = __d('nova', 'Elapsed Time: <b>{0}</b> sec | Memory Usage: <b>{1}</b> | SQL: <b>{2}</b> {3, plural, one{query} other{queries}} | UMAX: <b>{4}</b>', $elapsedStr, $memoryUsage, $queries, $queries, $umax);
        } else {
            $result = __d('nova', 'Elapsed Time: <b>{0}</b> sec | Memory Usage: <b>{1}</b> | UMAX: <b>{2}</b>', $elapsedStr, $memoryUsage, $umax);
        }

        return $result;
    }

    protected function getElapsedTime()
    {
        $timestamp = microtime(true);

        $requestTime = Request::server('REQUEST_TIME_FLOAT');

        return ($timestamp - $requestTime);
    }

    protected function getSqlQueries()
    {
        $withDatabase = $this->withDatabase();

        if (! $withDatabase) return 0;

        // Calculate and return the total SQL Queries.
        $connection = DB::connection();

        $queries = $connection->getQueryLog();

        return count($queries);
    }

    protected function withDatabase()
    {
        return array_get($this->config, 'withDatabase', false);
    }

    /**
     * Magic Method for handling dynamic functions.
     *
     * @param  string  $method
     * @param  array   $params
     * @return void|mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = new static();

        return call_user_func_array(array($instance, $method), $params);
    }
}
