<?php
/**
 * Queue Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */

return array(

    /*
    |--------------------------------------------------------------------------
    | Default Queue Driver
    |--------------------------------------------------------------------------
    |
    | The Laravel queue API supports a variety of back-ends via an unified
    | API, giving you convenient access to each back-end using the same
    | syntax for each one. Here you may set the default queue driver.
    |
    | Supported: "sync", "database", "beanstalkd", "sqs", "iron", "redis", "async"
    |
    */

    'default' => 'sync',

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    */

    'connections' => array(
        'sync' => array(
            'driver' => 'sync',
        ),
        'database' => array(
            'driver'    => 'database',
            'table'        => 'jobs',
            'queue'        => 'default',
            'expire'    => 60,
        ),
        'beanstalkd'    => array(
            'driver'    => 'beanstalkd',
            'host'        => 'localhost',
            'queue'        => 'default',
            'ttr'        => 60,
        ),
        'sqs' => array(
            'driver'    => 'sqs',
            'key'        => 'your-public-key',
            'secret'    => 'your-secret-key',
            'queue'        => 'your-queue-url',
            'region'    => 'us-east-1',
        ),
        'iron' => array(
            'driver'    => 'iron',
            'host'        => 'mq-aws-us-east-1.iron.io',
            'token'        => 'your-token',
            'project'    => 'your-project-id',
            'queue'        => 'your-queue-name',
            'encrypt'    => true,
        ),
        'redis' => array(
            'driver'    => 'redis',
            'queue'        => 'default',
        ),

        // NOTE: ONLY FOR RUNNING INCIDENTAL TASKS IN THE BACKGROUND!
        'async' => array(
            'driver'    => 'async',
            'table'        => 'jobs',
            'queue'        => 'default',
            'expire'    => 60,
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => array(
        'database'    => 'mysql',
        'table'        => 'failed_jobs',
    ),
);
