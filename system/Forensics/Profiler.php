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
    public $output = array();


    public function __construct($startTime)
    {
        $this->startTime = $startTime;
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

        if ($result === null) {
            $result = '%01.2f %s';
        }

        $lastSizeString = end($sizes);

        foreach ($sizes as $sizeString) {
            if ($size < 1024) {
                break;
            }

            if ($sizeString != $lastSizeString) {
                $size /= 1024;
            }
        }

        if ($sizeString == $sizes[0]) {
            $result = '%01d %s';  // Bytes aren't normally fractional
        }

        return sprintf($result, $size, $sizeString);
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
    public function display($connection = null)
    {
        $this->db = $connection;

        //
        $this->gatherConsoleData();
        $this->gatherFileData();
        $this->gatherMemoryData();
        $this->gatherQueryData();
        $this->gatherSpeedData();

        $this->renderProfiler($this->output);
    }

    /*
     * HTML Output for Php Quick Profiler
     */
    function renderProfiler($output)
    {
        $cssUrl = '/assets/css/profiler.css';

        //echo <<<JAVASCRIPT

        echo '<div id="pqp-container" class="pQp" style="display:none">';

        logCount = count($output['logs']['console']);

        $fileCount = count($output['files']);

        $memoryUsed = $output['memoryTotals']['used'];
        $queryCount = $output['queryTotals']['count'];
        $speedTotal = $output['speedTotals']['total'];

        echo '
<div id="pQp" class="console">
<table id="pqp-metrics" cellspacing="0">
<tr>
    <td class="green" onclick="changeTab(\'console\');">
        <var>$logCount</var>
        <h4>Console</h4>
    </td>
    <td class="blue" onclick="changeTab(\'speed\');">
        <var>$speedTotal</var>
        <h4>Load Time</h4>
    </td>
    <td class="purple" onclick="changeTab(\'queries\');">
        <var>$queryCount Queries</var>
        <h4>Database</h4>
    </td>
    <td class="orange" onclick="changeTab(\'memory\');">
        <var>$memoryUsed</var>
        <h4>Memory Used</h4>
    </td>
    <td class="red" onclick="changeTab(\'files\');">
        <var>{$fileCount} Files</var>
        <h4>Included</h4>
    </td>
</tr>
</table>';

        echo '<div id="pqp-console" class="pqp-box">';

        if($logCount ==  0) {
            echo '<h3>This panel has no log items.</h3>';
        }
        else {
            echo '<table class="side" cellspacing="0">
        <tr>
            <td class="alt1"><var>'.$output['logs']['logCount'].'</var><h4>Logs</h4></td>
            <td class="alt2"><var>'.$output['logs']['errorCount'].'</var> <h4>Errors</h4></td>
        </tr>
        <tr>
            <td class="alt3"><var>'.$output['logs']['memoryCount'].'</var> <h4>Memory</h4></td>
            <td class="alt4"><var>'.$output['logs']['speedCount'].'</var> <h4>Speed</h4></td>
        </tr>
        </table>
        <table class="main" cellspacing="0">';

            $class = '';
            foreach($output['logs']['console'] as $log) {
                echo '<tr class="log-'.$log['type'].'">
                <td class="type">'.$log['type'].'</td>
                <td class="'.$class.'">';

                if($log['type'] == 'log') {
                    echo '<div><pre>'.$log['data'].'</pre></div>';
                }
                elseif($log['type'] == 'memory') {
                    echo '<div><pre>'.$log['data'].'</pre> <em>'.$log['dataType'].'</em>: '.$log['name'].' </div>';
                }
                elseif($log['type'] == 'speed') {
                    echo '<div><pre>'.$log['data'].'</pre> <em>'.$log['name'].'</em></div>';
                }
                elseif($log['type'] == 'error') {
                    echo '<div><em>Line '.$log['line'].'</em> : '.$log['data'].' <pre>'.$log['file'].'</pre></div>';
                }

                echo '</td></tr>';

                if($class == '') $class = 'alt';

                else $class = '';
            }

            echo '</table>';
        }

        echo '</div>';

        echo '<div id="pqp-speed" class="pqp-box">';

        if($output['logs']['speedCount'] ==  0) {
            echo '<h3>This panel has no log items.</h3>';
        }
        else {
            echo '<table class="side" cellspacing="0">
          <tr><td><var>'.$output['speedTotals']['total'].'</var><h4>Load Time</h4></td></tr>
          <tr><td class="alt"><var>'.$output['speedTotals']['allowed'].'</var> <h4>Max Execution Time</h4></td></tr>
         </table>
        <table class="main" cellspacing="0">';

            $class = '';

            foreach($output['logs']['console'] as $log) {
                if($log['type'] == 'speed') {
                    echo '<tr class="log-'.$log['type'].'">
                <td class="'.$class.'">';

                echo '<div><pre>'.$log['data'].'</pre> <em>'.$log['name'].'</em></div>';

                echo '</td></tr>';

                    if($class == '') $class = 'alt';
                    else $class = '';
                }
            }

            echo '</table>';
        }

        echo '</div>';

        echo '<div id="pqp-queries" class="pqp-box">';

        if($output['queryTotals']['count'] ==  0) {
            echo '<h3>This panel has no log items.</h3>';
        }
        else {
            echo '<table class="side" cellspacing="0">
          <tr><td><var>'.$output['queryTotals']['count'].'</var><h4>Total Queries</h4></td></tr>
          <tr><td class="alt"><var>'.$output['queryTotals']['time'].'</var> <h4>Total Time</h4></td></tr>
          <tr><td><var>0</var> <h4>Duplicates</h4></td></tr>
         </table>
        <table class="main" cellspacing="0">';

            $class = '';

            foreach($output['queries'] as $query) {
                echo '<tr>
                <td class="'.$class.'">'.$query['sql'];
                if($query['explain']) {
                    echo '<em>
                        Possible keys: <b>'.$query['explain']['possible_keys'].'</b> &middot;
                        Key Used: <b>'.$query['explain']['key'].'</b> &middot;
                        Type: <b>'.$query['explain']['type'].'</b> &middot;
                        Rows: <b>'.$query['explain']['rows'].'</b> &middot;
                        Speed: <b>'.$query['time'].'</b>
                    </em>';
                }

                echo '</td></tr>';

                if($class == '') $class = 'alt';
                else $class = '';
            }

            echo '</table>';
        }

        echo '</div>';

        echo '<div id="pqp-memory" class="pqp-box">';

        if($output['logs']['memoryCount'] ==  0) {
            echo '<h3>This panel has no log items.</h3>';
        }
        else {
            echo '<table class="side" cellspacing="0">
          <tr><td><var>'.$output['memoryTotals']['used'].'</var><h4>Used Memory</h4></td></tr>
          <tr><td class="alt"><var>'.$output['memoryTotals']['total'].'</var> <h4>Total Available</h4></td></tr>
         </table>
        <table class="main" cellspacing="0">';

            $class = '';

            foreach($output['logs']['console'] as $log) {
                if($log['type'] == 'memory') {
                    echo '<tr class="log-'.$log['type'].'">';
                    echo '<td class="'.$class.'"><b>'.$log['data'].'</b> <em>'.$log['dataType'].'</em>: '.$log['name'].'</td>';
                    echo '</tr>';

                    if($class == '') $class = 'alt';
                    else $class = '';
                }
            }

            echo '</table>';
        }

        echo '</div>';

        echo '<div id="pqp-files" class="pqp-box">';

        if($output['fileTotals']['count'] ==  0) {
            echo '<h3>This panel has no log items.</h3>';
        }
        else {
            echo '<table class="side" cellspacing="0">
              <tr><td><var>'.$output['fileTotals']['count'].'</var><h4>Total Files</h4></td></tr>
            <tr><td class="alt"><var>'.$output['fileTotals']['size'].'</var> <h4>Total Size</h4></td></tr>
            <tr><td><var>'.$output['fileTotals']['largest'].'</var> <h4>Largest</h4></td></tr>
         </table>
        <table class="main" cellspacing="0">';

            $class ='';

            foreach($output['files'] as $file) {
                echo '<tr><td class="'.$class.'"><b>'.$file['size'].'</b> '.$file['name'].'</td></tr>';

                if($class == '') $class = 'alt';
                else $class = '';
            }

            echo '</table>';
        }

        echo '</div>';

        echo '
    <table id="pqp-footer" cellspacing="0">
        <tr>
            <td class="credit">
                <a href="http://particletree.com" target="_blank">
                <strong>PHP</strong>
                <b class="green">Q</b><b class="blue">u</b><b class="purple">i</b><b class="orange">c</b><b class="red">k</b>
                Profiler</a></td>
            <td class="actions">
                <a href="#" onclick="toggleDetails();return false">Details</a>
                <a class="heightToggle" href="#" onclick="toggleHeight();return false">Height</a>
            </td>
        </tr>
    </table>
';

        echo '</div></div>';
    }
}
