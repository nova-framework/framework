<?php

namespace Shared\Queue\Console;

use Nova\Console\Command;
use Nova\Queue\Worker;

use Shared\Queue\Queues\AsyncQueue;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


class AsyncCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:async';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a queue job from the database';

    /**
     * The queue worker instance.
     *
     * @var \Nova\Queue\Worker
     */
    protected $worker;


    /**
     * Create a new queue listen command.
     *
     * @param  \Nova\Queue\Worker  $worker
     * @return void
     */
    public function __construct(Worker $worker)
    {
        parent::__construct();

        $this->worker = $worker;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->argument('id');

        $connection = $this->argument('connection');

        $this->processJob(
            $connection, $id
        );
    }


    /**
     *  Process the job
     *
     */
    protected function processJob($connectionName, $id)
    {
        $manager = $this->worker->getManager();

        $connection = $manager->connection($connectionName);

        $job = $connection->getJobFromId($id);

        // If we're able to pull a job off of the stack, we will process it and
        // then immediately return back out. If there is no job on the queue
        // we will "sleep" the worker for the specified number of seconds.

        if (! is_null($job)) {
            $sleep = max($job->getDatabaseJob()->available_at - time(), 0);

            sleep($sleep);

            return $this->worker->process(
                $manager->getName($connectionName), $job
            );
        }

        return array('job' => null, 'failed' => false);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('id', InputArgument::REQUIRED, 'The Job ID'),

            array('connection', InputArgument::OPTIONAL, 'The name of connection'),
        );
    }

}
