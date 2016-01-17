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
use Nova\Config;

use Nova\Forensics\Console;
use Nova\Forensics\PdoDebugger;

use \PDO;


class Profiler
{
    protected $db = null;

    protected $viewPath;
    protected $startTime;

    public $output = array();


    public function __construct($connection = null)
    {
        $options = Config::get('profiler');

        if ($options['use_forensics'] != true) {
            return;
        }

        if($connection instanceof Connection) {
            $this->db = $connection;
        } else if($options['with_database'] == true) {
            $this->db = Database::getConnection();
        }

        // Setup the View path.
        $this->viewPath = APPPATH .'Views/Fragments/profiler.php';

        // Setup the Start Time.
        $this->startTime = FRAMEWORK_STARTING_MICROTIME;
    }

    public static function process($fetch = false)
    {
        $options = Config::get('profiler');

        if ($options['use_forensics'] != true) {
            return null;
        }

        Console::logSpeed('Forensics Profiler START');

        // The QuickProfiller was enabled into Configuration.
        $profiler = new self();

        return $profiler->display($connection, $fetch);
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
                'name' => str_replace(BASEPATH, '/', $file),
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
    public function gatherSQLQueryData()
    {
        $queryTotals = array();

        $queryTotals['count'] = 0;
        $queryTotals['time'] = 0;

        $queries = array();

        if($this->db !== null) {
            $queryTotals['count'] += $this->db->getTotalQueries();

            foreach($this->db->getExecutedQueries() as $key => $query) {
                if(isset($query['params']) && ! empty($query['params'])) {
                    $query['sql'] = PdoDebugger::show($query['sql'], $query['params']);
                }

                $query = $this->attemptToExplainQuery($query);

                $queryTotals['time'] += $query['time'];

                $query['time'] = $this->getReadableTime($query['time']);

                $queries[] = $query;
            }
        }

        $queryTotals['time'] = $this->getReadableTime($queryTotals['time']);

        $this->output['queries'] = $queries;
        $this->output['queryTotals'] = $queryTotals;
    }

    /*
     * Call SQL EXPLAIN on the Query to find more info.
     */
    function attemptToExplainQuery($query)
    {
        try {
            $statement = $this->db->query('EXPLAIN '.$query['sql']);

            if($statement !== false) {
                $query['explain'] = $statement->fetch(PDO::FETCH_ASSOC);
            }
        }
        catch(\Exception $e) {
            // Do nothing.
        }

        return $query;
    }

    /*
     * Speed data for entire page load.
     */
    public function gatherSpeedData()
    {
        $speedTotals = array();

        $speedTotals['total'] = $this->getReadableTime((microtime(true) - $this->startTime) * 1000);
        $speedTotals['allowed'] = ini_get("max_execution_time");

        $this->output['speedTotals'] = $speedTotals;
    }

    /*
     * Helper functions to format data.
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

        if(($time >= 1000) && ($time < 60000)) {
            $formatter = 1;

            $ret = ($time / 1000);
        }

        if($time >= 60000) {
            $formatter = 2;

            $ret = ($time / 1000) / 60;
        }

        return number_format($ret, 3, '.', '') .' ' .$formats[$formatter];
    }

    /*
     * Display to the screen (or return) the render output.
     */
    public function display($fetch = false)
    {
        // Gather the information.
        $this->gatherConsoleData();
        $this->gatherFileData();
        $this->gatherMemoryData();
        $this->gatherSQLQueryData();
        $this->gatherSpeedData();

        // Render the Profiler's widget.
        return $this->render($this->output, $fetch);
    }

    /*
     * HTML Output for Php Quick Profiler
     */
    function render($output, $fetch)
    {
        // Prepare the information.
        $logCount = count($output['logs']['console']);
        $fileCount = count($output['files']);

        $memoryUsed = $output['memoryTotals']['used'];
        $queryCount = $output['queryTotals']['count'];
        $speedTotal = $output['speedTotals']['total'];

        // Render the associated View Fragment (and return the output, if is the case).
        if($fetch) {
            ob_start();
        }

        require $this->viewPath;

        if($fetch) {
            return ob_get_clean();
        }

        return true;
    }
}
