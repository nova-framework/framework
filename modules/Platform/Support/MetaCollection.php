<?php

namespace Modules\Platform\Support;

use Nova\Database\ORM\Collection;


class MetaCollection extends Collection
{

    /**
     * Get an item from collection.
     *
     * @param mixed $name
     * @return mixed
     */
    public function getItem($name)
    {
        if (! is_null($key = $this->findItem($name))) {
            return $this->get($key);
        }
    }

    /**
     * Get the collection key form an item key.
     *
     * @param mixed $name
     * @return mixed
     */
    public function findItem($name)
    {
        $collection = $this->where('key', $name);

        if ($collection->count() > 0) {
            return $collection->keys()->first();
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (! is_null($key = $this->findItem($name))) {
            $item = $this->get($key);

            return $item->value;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        if (! is_null($key = $this->findItem($name))) {
            $item = $this->get($key);

            return ! is_null($item->value);
        }

        return false;
    }
}
