<?php

namespace App\Modules\Fields\Models\Collection;

use Nova\Database\ORM\Collection as BaseCollection;
use Nova\Database\ORM\Model;
use Nova\Support\Str;

use App\Modules\Fields\Models\MetaData as MetaItem;

use InvalidArgumentException;


class MetaCollection extends BaseCollection
{
    /**
     * Keys of the models that the collection was constructed with.
     *
     * @var array
     */
    protected $originalModelKeys = array();


    /**
     * MetaItemCollection constructor.
     *
     * @param array $items
     */
    public function __construct($items = array())
    {
        parent::__construct($items);

        $this->originalModelKeys = $this->modelKeys();

        $this->observeDeletions($this->items);
    }

    /**
     * Get the array of primary keys.
     *
     * @return array
     */
    public function modelKeys()
    {
        $keys = array();

        foreach ($this->items as $item) {
            if ($item instanceof Model) {
                $keys[] = $item->getKey();
            }
        }

        return $keys;
    }

    /**
     * Get the array of primary keys the collection was constructed with.
     *
     * @return array
     */
    public function originalModelKeys()
    {
        return $this->originalModelKeys;
    }

    /**
     * Add an item to the collection.
     *
     * @param mixed $item
     * @return $this
     * @throws InvalidArgumentException
     */
    public function add($item)
    {
        if ($item instanceof MetaItem) {
            if (! is_null($this->findItem($item->key))) {
                $key = $item->key;

                throw new InvalidArgumentException("Unique key constraint failed. [$key]");
            }

            $this->observeDeletions(array($item));
        }

        $this->items[] = $item;

        return $this;
    }

    /**
     * Resolve calls to set a new item to the collection or update an existing key.
     *
     * @param $name
     * @param $value
     * @param $type
     */
    public function updateOrAdd($name, $value, $type = null)
    {
        if (! is_null($key = $this->findItem($name))) {
            $item = $this->get($key);

            $item->setValueAttribute($value, $type);
        } else {
            $this->addItem($name, $value, $type);
        }
    }

    /**
     * Add an item to the collection.
     *
     * @param string $name
     * @param mixed $value
     * @param mixed $type
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addItem($name, $value, $type = null)
    {
        $item = new MetaItem(array(
            'key' => $name,
        ));

        $item->setValueAttribute($value, $type);

        $this->add($item);

        return $this;
    }

    /**
     * Get the collection key form an item key and tag.
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
     * Set deletion listeners on an array of items.
     *
     * @param array $items
     */
    protected function observeDeletions(array $items)
    {
        foreach ($items as $item) {
            if ($item instanceof MetaItem) {
                $this->observeSaving($item);

                $this->observeDeletion($item);
            }
        }
    }

    /**
     * Set a deletion listener on an item.
     *
     * @param \App\Modules\Fields\Models\MetaData $item
     */
    protected function observeSaving(MetaItem $item)
    {
        $item::saving(function ($model)
        {
            $model->getTypeInstance()->cleanup();
        });
    }

    /**
     * Set a deletion listener on an item.
     *
     * @param \App\Modules\Fields\Models\MetaData $item
     */
    protected function observeDeletion(MetaItem $item)
    {
        $item::deleting(function ($model)
        {
            $model->getTypeInstance()->cleanup(true);
        });

        $item::deleted(function ($model)
        {
            if (! is_null($key = $this->findItem($model->name))) {
                $this->forget($key);
            }
        });
    }

    /**
     * Resolve calls to check whether an item with a specific key name exists.
     *
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return ! is_null($this->findItem($name));
    }

    /**
     * Resolve calls to unset an item with a specific key name.
     *
     * @param $name
     */
    public function __unset($name)
    {
        if (! is_null($key = $this->findItem($name))) {
            $this->forget($key);
        }
    }

    /**
     * Resolve calls to get an item with a specific key name.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (! is_null($key = $this->findItem($name))) {
            $item = $this->get($key);

            return $item->getTypeInstance();
        }
    }

    /**
     * Resolve calls to set a new item to the collection or update an existing key.
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (! is_null($key = $this->findItem($name))) {
            $item = $this->get($key);

            $item->value = $value;

            return;
        }

        $this->addItem($name, $value);
    }
}
