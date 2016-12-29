<?php
/**
 * Profiler Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


return array(

    /*
    |--------------------------------------------------------------------------
    | Enable Forensics
    |--------------------------------------------------------------------------
    |
    | You can set these options to true, if you need help with debugging.
    | By default, the number of database sql queries is logged in the profiler
    | seen at the bottom of the default theme.
    |
    | When the debug setting is enabled in app/Config/App.php (on by default)
    | the following metrics are shown on the default and AdminLTE themes:
    |
    | Elapsed Time: 0.1120 sec | Memory Usage: 4.35MB | SQL: 0 queries | UMAX: 223
    |
    | Elapsed Time: Total time executed in seconds.
    | Memory Usage: Total memory in MB used.
    | SQL: When withDatabase setting is true this displays total number of queries ran.
    | UMAX: represents an estimation of the maximum number of pages served
    | per second. You can use UMAX as a general speed evaluation on a reasonable
    | server load. Bigger the number given by UMAX, the better it is.
    |
    */

    'useForensics' => false,
    'withDatabase' => false,
);
