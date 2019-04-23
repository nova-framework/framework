<?php

namespace Shared\Cache\Jobs;

use Nova\Bus\QueueableTrait;
use Nova\Queue\SerializesModelsTrait;
use Nova\Queue\InteractsWithQueueTrait;
use Nova\Queue\ShouldQueueInterface;
use Nova\Support\Facades\Cache;


class FlushTagFromFileCache implements ShouldQueueInterface
{
    use InteractsWithQueueTrait, QueueableTrait, SerializesModelsTrait;

    /**
     * @var array
     */
    protected $tagIds;


    /**
     * Create a new job instance.
     *
     * @param mixed  $ids
     * @param string $driver
     */
    public function __construct($ids)
    {
        $this->tagIds = is_array($ids) ? $ids : array($ids);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cache = Cache::driver('taggedFile');

        foreach ($this->tagIds as $id) {
            $cache->flushOldTag($id);
        }
    }
}
