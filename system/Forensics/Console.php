<?php
/**
 * Console
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 15th, 2016
 */

/* - - - - - - - - - - - - - - - - - - - - -

 Title : PHP Quick Profiler Console Class
 Author : Created by Ryan Campbell
 URL : http://particletree.com/features/php-quick-profiler/

 Last Updated : April 22, 2009

 Description : This class serves as a wrapper around a global
 php variable, debugger_logs, that we have created.

- - - - - - - - - - - - - - - - - - - - - */

namespace Nova\Forensics;


class Console
{
    /*
     * Contains all of the logs that are collected.
     */
    private static $logs = array(
        'console'     => array(),
        'logCount'    => 0,
        'memoryCount' => 0,
        'errorCount'  => 0,
        'speedCount'  => 0,
    );

    /*
     *  Log a variable to console.
     */
    public static function log($data)
    {
        $logItem = array(
            "data" => $data,
            "type" => 'log'
        );

        self::$logs['console'][] = $logItem;

        self::$logs['logCount'] += 1;
    }

    /*
     * Log memory usage of variable or entire script.
     */
    public function logMemory($object = false, $name = 'PHP')
    {
        $memory = memory_get_usage();

        if($object) $memory = strlen(serialize($object));

        $logItem = array(
            "data" => $memory,
            "type" => 'memory',
            "name" => $name,
            "dataType" => gettype($object)
        );

        self::$logs['console'][] = $logItem;

        self::$logs['memoryCount'] += 1;
    }

    /*
     * Log a php exception object.
     */
    public function logError($exception, $message)
    {
        $logItem = array(
            "data" => $message,
            "type" => 'error',
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        );

        self::$logs['console'][] = $logItem;

        self::$logs['errorCount'] += 1;
    }

    /*
     * Point in time speed snapshot.
     */
    public function logSpeed($name = 'Point in Time')
    {
        $logItem = array(
            "data" => PhpQuickProfiler::getMicroTime(),
            "type" => 'speed',
            "name" => $name
        );

        self::$logs['console'][] = $logItem;

        self::$logs['speedCount'] += 1;
    }

    /*
     * Return the logs.
     */
    public function getLogs()
    {
        return self::$logs;
    }
    
}
