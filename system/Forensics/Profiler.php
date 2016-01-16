<?php
/**
 * Profiler
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 15th, 2016
 */

/* - - - - - - - - - - - - - - - - - - - - -

 Title : PHP Quick Profiler Class
 Author : Created by Ryan Campbell
 URL : http://particletree.com/features/php-quick-profiler/

 Last Updated : April 22, 2009

 Description : This class processes the logs and organizes the data
 for output to the browser. Initialize this class with a start time at
 the beginning of your code, and then call the display method when your code
 is terminating.

- - - - - - - - - - - - - - - - - - - - - */

namespace Nova\Forensics;

use Nova\Database\Connection;
use Nova\Database\Manager as Database;

use Nova\Forensics\Console;


class Profiler
{
    protected $instance = null;

    public $output = array();


    public function __construct($connection = null)
    {
        $this->startTime = FRAMEWORK_STARTING_MICROTIME;

        $this->db = $connection;
    }

    /*
     * Format the different types of logs.
     */
    public function gatherConsoleData()
    {
        $logs = Console::getLogs();

        if(isset($logs['console'])) {
            foreach($logs['console'] as $key => $log) {
                if($log['type'] == 'log') {
                    $logs['console'][$key]['data'] = print_r($log['data'], true);
                }
                else if($log['type'] == 'memory') {
                    $logs['console'][$key]['data'] = $this->getReadableFileSize($log['data']);
                }
                else if($log['type'] == 'speed') {
                    $logs['console'][$key]['data'] = $this->getReadableTime(($log['data'] - $this->startTime) * 1000);
                }
            }
        }

        $this->output['logs'] = $logs;
    }

    /*
     * Aggregate data on the files included.
     */
    public function gatherFileData()
    {
        $files = get_included_files();

        $fileList = array();

        $fileTotals = array(
            "count" => count($files),
            "size" => 0,
            "largest" => 0,
        );

        foreach($files as $key => $file) {
            $size = filesize($file);

            $fileList[] = array(
                'name' => $file,
                'size' => $this->getReadableFileSize($size)
            );

            $fileTotals['size'] += $size;

            if($size > $fileTotals['largest']) $fileTotals['largest'] = $size;
        }

        $fileTotals['size'] = $this->getReadableFileSize($fileTotals['size']);
        $fileTotals['largest'] = $this->getReadableFileSize($fileTotals['largest']);

        $this->output['files'] = $fileList;
        $this->output['fileTotals'] = $fileTotals;
    }

    /*
     * Memory usage and memory available.
     */
    public function gatherMemoryData()
    {
        $memoryTotals = array();

        $memoryTotals['used'] = $this->getReadableFileSize(memory_get_peak_usage());

        $memoryTotals['total'] = ini_get("memory_limit");

        $this->output['memoryTotals'] = $memoryTotals;
    }

    /*
     * QUERY DATA -- Database object with logging required
     */
    public function gatherQueryData()
    {
        $queryTotals = array();

        $queryTotals['count'] = 0;
        $queryTotals['time'] = 0;

        $queries = array();

        if($this->db !== null) {
            /*
            $queryTotals['count'] += $this->db->queryCount;

            foreach($this->db->queries as $key => $query) {
                $query = $this->attemptToExplainQuery($query);
                $queryTotals['time'] += $query['time'];
                $query['time'] = $this->getReadableTime($query['time']);
                $queries[] = $query;
            }
            */
        }

        $queryTotals['time'] = $this->getReadableTime($queryTotals['time']);

        $this->output['queries'] = $queries;
        $this->output['queryTotals'] = $queryTotals;
    }

    /*
     * Call sql explain on the query to find more info
     */
    function attemptToExplainQuery($query)
    {
        /*
        try {
            $sql = 'EXPLAIN '.$query['sql'];
            $rs = $this->db->query($sql);
        }
        catch(Exception $e) {}
        if($rs) {
            $row = mysql_fetch_array($rs, MYSQL_ASSOC);
            $query['explain'] = $row;
        }
        */
        return $query;
    }

    /*
     * Speed data for entire page load
     */
    public function gatherSpeedData()
    {
        $speedTotals = array();

        $speedTotals['total'] = $this->getReadableTime((microtime(true) - $this->startTime) * 1000);
        $speedTotals['allowed'] = ini_get("max_execution_time");

        $this->output['speedTotals'] = $speedTotals;
    }

    /*
     * Helper functions to format data
     */
    public function getReadableFileSize($size, $result = null)
    {
        // Adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
        $sizes = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

        if ($result === null) $result = '%01.2f %s';

        $lastSizeStr = end($sizes);

        foreach ($sizes as $sizeStr) {
            if ($size < 1024) break;

            if ($sizeStr != $lastSizeStr) $size /= 1024;
        }

        if ($sizeStr == $sizes[0]) $result = '%01d %s';  // Bytes aren't normally fractional

        return sprintf($result, $size, $sizeStr);
    }

    public function getReadableTime($time)
    {
        $ret = $time;
        $formatter = 0;

        $formats = array('ms', 's', 'm');

        if($time >= 1000 && $time < 60000) {
            $formatter = 1;

            $ret = ($time / 1000);
        }

        if($time >= 60000) {
            $formatter = 2;

            $ret = ($time / 1000) / 60;
        }

        $ret = number_format($ret, 3, '.', '') .' ' .$formats[$formatter];

        return $ret;
    }

    /*
     * Display to the screen -- CALL WHEN CODE TERMINATING
     */
    public static function display($connection = null)
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }

        self::$instance;

        //
        self::$instance->gatherConsoleData();
        self::$instance->gatherFileData();
        self::$instance->gatherMemoryData();
        self::$instance->gatherQueryData();
        self::$instance->gatherSpeedData();

        return self::$instance->render();
    }

    /*
     * HTML Output for Php Quick Profiler
     */
    function render()
    {
        $output =& $this->output;

        //
        $viewPath = APPPATH .'Views/Fragments/profiler.php';

        $logCount = count($output['logs']['console']);
        $fileCount = count($output['files']);

        $memoryUsed = $output['memoryTotals']['used'];
        $queryCount = $output['queryTotals']['count'];
        $speedTotal = $output['speedTotals']['total'];

        // Render the Profiler Fragment and return the output.
        ob_start();

        require $viewPath;

        return ob_get_clean();
    }
}
