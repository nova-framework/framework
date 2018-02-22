<?php

namespace Shared\Queue;

use Nova\Queue\Job;
use Nova\Queue\Worker;
use Nova\Queue\WorkerOptions;

use Shared\Queue\StopBatchException;


class BatchRunner extends Worker
{
    /**
     * @var int
     */
    protected $timeLimit;

    /**
     * @var int
     */
    protected $jobLimit;

    /**
     * @var int
     */
    protected $jobCount;

    /**
     * @var float
     */
    protected $startTime;


    /**
     * Listen to the given queue in a loop.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  int     $delay
     * @param  int     $memory
     * @param  int     $sleep
     * @param  int     $maxTries
     * @param  int     $timeLimit
     * @param  int     $jobLimit
     * @return void
     */
    public function batch($connectionName, $queue, $delay, $memory, $sleep, $maxTries, $timeLimit, $jobLimit)
    {
        $this->timeLimit = $timeLimit;
        $this->jobLimit  = $jobLimit;

        $this->startTime = microtime(true);
        $this->jobCount  = 0;

        try {
           $this->daemon($connectionName, $queue, $delay, $memory, $sleep, $maxTries);
        }
        catch (StopBatchException $e) {
            // The batch hit a limit.
        }
    }

    /**
     * Raise the after queue job event.
     *
     * @param  string  $connectionName
     * @param  \Nova\Contracts\Queue\Job  $job
     * @return void
     */
    protected function raiseAfterJobEvent($connectionName, Job $job)
    {
        $this->jobCount++;

        parent::raiseAfterJobEvent($connectionName, $job);

        $this->checkLimits();
    }

    /**
     * Sleep the script for a given number of seconds.
     *
     * @param  int   $seconds
     * @return void
     */
    public function sleep($seconds)
    {
        $this->checkLimits();

        parent::sleep($seconds);
    }

    /**
     * Stop listening and bail out of the script.
     *
     * @return void
     */
    public function stop($status = 0)
    {
        $this->events->fire('nova.queue.stopping');

        // Cleanly handle stopping a batch without resorting to killing the process
        throw new StopBatchException();
    }

    /**
     * Check our batch limits and stop the command if we reach a limit.
     *
     * @param  WorkerOptions $options
     */
    protected function checkLimits()
    {
        if ($this->isTimeLimit($this->timeLimit) || $this->isJobLimit($this->jobLimit)) {
            $this->stop();
        }
    }

    /**
     * Check if the batch timelimit has been reached.
     *
     * @param  init     $timeLimit
     *
     * @return boolean
     */
    protected function isTimeLimit($timeLimit)
    {
        return ((microtime(true) - $this->startTime) > $timeLimit);
    }

    /**
     * Check if the batch job limit has been reached.
     *
     * @param  int        $jobLimit
     *
     * @return boolean
     */
    protected function isJobLimit($jobLimit)
    {
        return ($this->jobCount >= $jobLimit);
    }

}
