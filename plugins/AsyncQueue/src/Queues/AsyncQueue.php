<?php
namespace AsyncQueue\Queues;

use Nova\Database\Connection;
use Nova\Queue\Queues\DatabaseQueue;
use Nova\Queue\Jobs\DatabaseJob;

use Symfony\Component\Process\Process;

use Carbon\Carbon;

use DateTime;


class AsyncQueue extends DatabaseQueue
{
    /**
     * @var string
     */
    protected $binary;

    /**
     * @var string
     */
    protected $binaryArgs;

    /**
     * @var string
     */
    protected $connectionName;


    /**
     * @param  \Nova\Database\Connection  $database
     * @param  string  $table
     * @param  string  $default
     * @param  int  $expire
     * @param  string  $binary
     * @param  string|array  $binaryArgs
     */
    public function __construct(Connection $database,
                                $table,
                                $default = 'default',
                                $expire = 60,
                                $binary = 'php',
                                $binaryArgs = '',
                                $connectionName = '')
    {
        parent::__construct($database, $table, $default, $expire);

        $this->binary = $binary;

        $this->binaryArgs = $binaryArgs;

        $this->connectionName = $connectionName;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string      $job
     * @param mixed       $data
     * @param string|null $queue
     *
     * @return int
     */
    public function push($job, $data = '', $queue = null)
    {
        $id = parent::push($job, $data, $queue);

        $this->startProcess($id);

        return $id;
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string  $queue
     * @param  array   $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = array())
    {
        $id = parent::pushRaw($payload, $queue, $options);

        $this->startProcess($id);

        return $id;
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTime|int $delay
     * @param string        $job
     * @param mixed         $data
     * @param string|null   $queue
     *
     * @return int
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $id = parent::later($delay, $job, $data, $queue);

        $this->startProcess($id);

        return $id;
    }

    protected function pushToDatabase($delay, $queue, $payload, $attempts = 0)
    {
        $availableAt = ($delay instanceof DateTime) ? $delay : Carbon::now()->addSeconds($delay);

        $attributes = array(
            'queue'        => $this->getQueue($queue),
            'payload'      => $payload,
            'attempts'     => $attempts,
            'reserved'     => 1,
            'reserved_at'  => $this->getTime(),
            'available_at' => $availableAt->getTimestamp(),
            'created_at'   => $this->getTime(),
        );

        return $this->getQuery()->insertGetId($attributes);
    }

    /**
     * Get the next available job for the queue.
     *
     * @param  string|null  $queue
     * @return \StdClass|null
     */
    public function getJobFromId($id)
    {
        $job = $this->getQuery()->where('id', $id)->first();

        if(! is_null($job)) {
            return new DatabaseJob(
                $this->container, $this, $job, $job->queue
            );
        }
    }

    /**
     * Make a Process for the Forge command for the job id.
     *
     * @param int $jobId
     * @param int $delay
     *
     * @return void
     */
    public function startProcess($id)
    {
        $command = $this->getCommand($id);

        $cwd = base_path();

        $process = new Process($command, $cwd);

        $process->run();
    }

    /**
     * Get the Forge command as a string for the job id.
     *
     * @param int $jobId
     * @param int $delay
     *
     * @return string
     */
    protected function getCommand($id)
    {
        $connection = $this->connectionName;

        $cmd = '%s forge queue:async %d %s';

        $cmd = $this->getBackgroundCommand($cmd);

        $binary = $this->getPhpBinary();

        return sprintf($cmd, $binary, $id, $connection);
    }

    /**
     * Get the escaped PHP Binary from the configuration
     *
     * @return string
     */
    protected function getPhpBinary()
    {
        $path = $this->binary;

        if (! windows_os()) {
            $path = escapeshellarg($path);
        }

        $args = $this->binaryArgs;

        if(is_array($args)) {
            $args = implode(' ', $args);
        }

        return trim($path .' ' .$args);
    }

    protected function getBackgroundCommand($cmd)
    {
        if ( windows_os()) {
            return 'start /B '.$cmd.' > NUL';
        } else {
            return $cmd.' > /dev/null 2>&1 &';
        }
    }



}
