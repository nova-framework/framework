<?php

namespace Shared\Queue\Console;

use Nova\Queue\Console\WorkCommand;

use Shared\Queue\BatchRunner;

use Symfony\Component\Console\Input\InputOption;


class BatchCommand extends WorkCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:batch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processing jobs on the queue as single once off batch';


    /**
     * Create a new queue listen command.
     *
     * @param  \Nova\Queue\Worker  $worker
     * @return void
     */
    public function __construct(BatchRunner $worker)
    {
        parent::__construct($worker);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->downForMaintenance()) {
            $sleep = $this->option('sleep');

            return $this->worker->sleep($sleep);
        }

        $queue = $this->option('queue');
        $delay = $this->option('delay');

        // The memory limit is the amount of memory we will allow the script to occupy
        // before killing it and letting a process manager restart it for us, which
        // is to protect us against any memory leaks that will be in the scripts.
        $memory = $this->option('memory');

        if (empty($connection = $this->argument('connection')) {
            $connection = $this->container['config']['queue.default'];
        }

        $this->runWorker($connection, $queue, $delay, $memory);
    }

    /**
     * Run the worker instance.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  int  $delay
     * @param  int  $memory
     * @param  bool  $daemon
     * @return array
     */
    protected function runWorker($connection, $queue, $delay, $memory, $daemon = false)
    {
        $this->worker->setDaemonExceptionHandler(
            $this->container['Nova\Foundation\Contracts\ExceptionHandlerInterface']
        );

        $sleep = $this->option('sleep');
        $tries = $this->option('tries');

        $this->worker->setCache(
            $this->container['cache']->driver()
        );

        return $this->worker->batch(
            $connection, $queue, $delay,$memory, $sleep, $tries,
            $this->option('time-limit'), $this->option('job-limit')
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('queue',  null, InputOption::VALUE_OPTIONAL, 'The queue to listen on'),
            array('daemon', null, InputOption::VALUE_NONE,     'Run the worker in daemon mode'),
            array('delay',  null, InputOption::VALUE_OPTIONAL, 'Amount of time to delay failed jobs', 0),
            array('force',  null, InputOption::VALUE_NONE,     'Force the worker to run even in maintenance mode'),
            array('memory', null, InputOption::VALUE_OPTIONAL, 'The memory limit in megabytes', 128),
            array('sleep',  null, InputOption::VALUE_OPTIONAL, 'Number of seconds to sleep when no job is available', 3),
            array('tries',  null, InputOption::VALUE_OPTIONAL, 'Number of times to attempt a job before logging it failed', 0),

            //
            array('time-limit', null, InputOption::VALUE_OPTIONAL, 'The max time in seconds the batch should run for', 60),
            array('job-limit',  null, InputOption::VALUE_OPTIONAL, 'The maximum number of Jobs that the batch should process', 100),
        );
    }

}
