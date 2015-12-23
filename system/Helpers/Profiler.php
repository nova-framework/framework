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
use Nova\Config;

class Profiler
{

    public static function report()
    {
        $options = Config::get('profiler');

        // Calculate the variables.
        $exectime = microtime(true) - FRAMEWORK_STARTING_MICROTIME;

        $elapsed_time = sprintf("%01.4f", $exectime);

        $memory_usage = Number::humanSize(memory_get_usage());

        if($options['with_queries'] == true) {
            $engine = Manager::getEngine();

            $total_queries = $engine->getTotalQueries();

            $queries_str = ($total_queries == 1) ? __d('system', 'query') : __d('system', 'queries');
        }
        else {
            $total_queries = 0;

            $queries_str = __d('system', 'queries');
        }

        $estimated_users = sprintf("%0d", intval(25 / $exectime));

        //
        $retval = __d('system', 'Elapsed Time: <b>{0}</b> sec | Memory Usage: <b>{1}</b> | SQL: <b>{2}</b> {3} | UMAX: <b>{4}</b>',
                        $elapsed_time,
                        $memory_usage,
                        $total_queries,
                        $queries_str,
                        $estimated_users
                    );

        return $retval;
    }

}
