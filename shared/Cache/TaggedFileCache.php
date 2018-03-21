<?php

namespace Shared\Cache;

use Nova\Cache\TaggedCache;


class TaggedFileCache extends TaggedCache
{

    /**
     * Get a fully qualified key for a tagged item.
     *
     * @param  string $key
     *
     * @return string
     */
    public function taggedItemKey($key)
    {
        return $this->tags->getNamespace() . $this->store->separator . $key;
    }
}
