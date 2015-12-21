<?php
/*
 * Profiler Helper - generate a Profiler information
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 1.0
 * @date Dec 21 2015
 */

namespace Nova\Helpers;


use Nova\Database\Manager;


class Profiler
{

    public static function report()
    {
        $engine = Manager::getEngine();

        $exectime = microtime(true) - FRAMEWORK_STARTING_MICROTIME;

        $elapsed_time = sprintf("%01.4f", $exectime);

        $memory_usage = Number::humanSize(memory_get_usage());

        $total_queries = $engine->getTotalQueries();

        $queries_str = ($total_queries == 1) ? __('query') : __('queries');

        $estimated_users = sprintf("%0d", intval(25 / $exectime));

        //
        $retval = __('Elapsed Time: <b>{0}</b> sec | Memory Usage: <b>{1}</b> | SQL: <b>{2}</b> %s | UMAX: <b>{3}</b>',
                        $elapsed_time,
                        $memory_usage,
                        $total_queries,
                        $queries_str,
                        $estimated_users
                    );

        return $retval;
    }

}
