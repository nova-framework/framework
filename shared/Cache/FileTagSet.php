<?php

namespace Shared\Cache;

use Nova\Cache\TagSet;

use Shared\Cache\Jobs\FlushTagFromFileCache;


class FileTagSet extends TagSet
{

    /**
     * Get the tag identifier key for a given tag.
     *
     * @param  string $name
     *
     * @return string
     */
    public function tagKey($name)
    {
        return 'cache_tags' .$this->store->separator .$name;
    }

    /**
     * Reset the tag and return the new tag identifier.
     *
     * @param  string $name
     *
     * @return string
     */
    public function resetTag($name)
    {
        $key = $this->tagKey($name);

        if (! is_null($id = $this->store->get($key))) {
            $job = new FlushTagFromFileCache($id);

            if (! empty($this->store->queue)) {
                $job->onQueue($this->store->queue);
            }

            dispatch($job);
        }

        return parent::resetTag($name);
    }
}
