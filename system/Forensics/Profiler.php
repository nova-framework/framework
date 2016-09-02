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

namespace Forensics;

use Config\Config;

use Forensics\Console;
use Forensics\PdoDebugger;

use DB;
use Request;

use PDO;


class Profiler
{
    protected $connection;

    protected $viewPath;
    protected $startTime;

    public $output = array();


    public function __construct($connection = null)
    {
        $config = Config::get('profiler');

        if ($config['useForensics'] != true) {
            return;
        }

        if($connection instanceof Connection) {
            $this->connection = $connection;
        } else if($config['withDatabase'] == true) {
            $this->connection = DB::connection();
        }

        // Setup the View path.
        $this->viewPath = realpath(__DIR__) .DS .'Views' .DS .'Profiler.php';

        // Setup the Start Time.
        $this->startTime = Request::server('REQUEST_TIME_FLOAT');
    }

    public static function process($fetch = false)
    {
        $config = Config::get('profiler');

        if ($config['useForensics'] != true) {
            return null;
        }

        // The QuickProfiller was enabled into Configuration.
        $profiler = new static();

        return $profiler->display($fetch);
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
                'name' => str_replace(ROOTDIR, '/', $file),
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

        if(isset($this->connection)) {
            $queryLog = $this->connection->getQueryLog();

            $queryTotals['count'] += count($queryLog);

            foreach($queryLog as $query) {
                if(isset($query['bindings']) && ! empty($query['bindings'])) {
                    $query['sql'] = PdoDebugger::show($query['query'], $query['bindings']);
                } else {
                    $query['sql'] = $query['query'];
                }

                $query = $this->attemptToExplainQuery($query);

                $queryTotals['time'] += $query['time'];

                $query['time'] = $this->getReadableTime($query['time']);

                //
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
            $statement = $this->connection->getPdo()->prepare('EXPLAIN ' .$query['query']);

            if($statement !== false) {
                $statement->execute();

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
     * Server variables and Configuration.
     */
    public function gatherFrameworkData()
    {
        $output = array();

        // GET variables
        if (count($_GET) == 0) {
            $output['get'] = __d('nova', 'No GET data exists');
        } else {
            $output['get'] = array();

            foreach ($_GET as $key => $value) {
                if (! is_numeric($key)) {
                    $key = "'".$key."'";
                }

                if (is_array($value)) {
                    $output['get']['&#36;_GET['. $key .']'] = '<pre>'. htmlspecialchars(stripslashes(print_r($value, TRUE))) .'</pre>';
                } else {
                    $output['get']['&#36;_GET['. $key .']'] = htmlspecialchars(stripslashes($value));
                }
            }
        }

        // POST variables
        if (count($_POST) == 0) {
            $output['post'] = __d('nova', 'No POST data exists');
        } else {
            $output['post'] = array();

            foreach ($_POST as $key => $value) {
                if (! is_numeric($key)) {
                    $key = "'".$key."'";
                }

                if (is_array($value)) {
                    $output['post']['&#36;_POST['. $key .']'] = '<pre>'. htmlspecialchars(stripslashes(print_r($value, TRUE))) .'</pre>';
                } else {
                    $output['post']['&#36;_POST['. $key .']'] = htmlspecialchars(stripslashes($value));
                }
            }
        }

        // Server Headers
        $output['headers'] = array();

        $headers = array(
            'HTTP_ACCEPT',
            'HTTP_USER_AGENT',
            'HTTP_CONNECTION',
            'SERVER_PORT',
            'SERVER_NAME',
            'REMOTE_ADDR',
            'SERVER_SOFTWARE',
            'HTTP_ACCEPT_LANGUAGE',
            'SCRIPT_NAME',
            'REQUEST_METHOD',
            ' HTTP_HOST',
            'REMOTE_HOST',
            'CONTENT_TYPE',
            'SERVER_PROTOCOL',
            'QUERY_STRING',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_X_FORWARDED_FOR'
        );

        foreach ($headers as $header) {
            $value = (isset($_SERVER[$header])) ? $_SERVER[$header] : '';

            $output['headers'][$header] = $value;
        }

        // Store the information.
        $this->output['variables'] = $output;
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
        Console::log(__d('nova', 'Forensics - Profiler start gathering the information'));

        // Gather the information.
        $this->gatherFileData();
        $this->gatherMemoryData();
        $this->gatherSQLQueryData();
        $this->gatherFrameworkData();

        Console::logSpeed(__d('nova', 'Forensics - Profiler start displaying the information'));

        $this->gatherConsoleData();
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
