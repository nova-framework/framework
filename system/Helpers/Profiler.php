<?php
/*
 * Profiler Helper - generate a Profiler information
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Helpers;

use Core\Config;

use DB;
use Request;


class Profiler
{

    public static function getReport()
    {
        $options = Config::get('profiler');

        // Calculate the variables.
        $execTime = microtime(true) - Request::server('REQUEST_TIME_FLOAT');

        $elapsedTime = sprintf("%01.4f", $execTime);

        $memoryUsage = Number::humanSize(memory_get_usage());

        if ($options['withDatabase'] == true) {
            $connection = DB::connection();

            $queries = $connection->getQueryLog();

            $totalQueries = count($queries);

            $queriesStr = ($totalQueries == 1) ? __d('nova', 'query') : __d('nova', 'queries');
        } else {
            $totalQueries = 0;

            $queriesStr = __d('nova', 'queries');
        }

        $estimatedUsers = sprintf("%0d", intval(25 / $execTime));

        //
        $retval = __d('nova', 'Elapsed Time: <b>{0}</b> sec | Memory Usage: <b>{1}</b> | SQL: <b>{2}</b> {3} | UMAX: <b>{4}</b>', $elapsedTime, $memoryUsage, $totalQueries, $queriesStr, $estimatedUsers);

        return $retval;
    }
}
