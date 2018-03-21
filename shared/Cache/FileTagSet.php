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
        $name = preg_replace('/[^\w\s\d\-_~,;\[\]\(\).]/', '~', $name);

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
        $id = $this->store->get($this->tagKey($name));

        if ($id !== false) {
            $job = new FlushTagFromFileCache($id);

            if (! empty($this->store->queue)) {
                $job->onQueue($this->store->queue);
            }

            dispatch($job);
        }

        return parent::resetTag($name);
    }
}
