<?php

namespace App\Modules\Fields\Traits;

use Nova\Database\ORM\Relations\HasMany;
use Nova\Database\ORM\Collection;
use Nova\Support\Facades\Schema;
use Nova\Support\Collection as BaseCollection;
use Nova\Support\Str;

use App\Modules\Fields\Models\MetaData;
use App\Modules\Fields\Traits\HasMetaTrait;

use InvalidArgumentException;


trait MetableTrait
{
    use HasMetaTrait;

    /**
     * Model table columns.
     *
     * @var array
     */
    public static $tableColumns = array();


    abstract public function getMetaFields();


    /**
     * Get a models table columns.
     *
     * @param $class
     * @return mixed
     */
    protected function getTableColumns()
    {
        $class = static::class;

        if (isset(static::$tableColumns[$class])) {
            return static::$tableColumns[$class];
        }

        $table = $this->getTable();

        return static::$tableColumns[$class] = $this->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($table);
    }

    /**
     * Check whether a method, property or attribute
     * name exists on the model.
     *
     * @param $name
     * @return bool
     */
    protected function existsOnParent($name)
    {
        return property_exists($this, $name)
            || method_exists($this, $name)
            || ! is_null($this->getAttribute($name))
            || in_array($name, $this->getTableColumns());
    }

    /**
     * Dynamically determine whether a meta item or model property isset.
     *
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        if ($this->existsOnParent($name)) {
            return parent::__isset($name);
        }

        // Does not exists in parent.
        else if (isset($this->meta)) {
            return ! is_null($this->meta->findItem($name));
        }
    }

    /**
     * Dynamically get a model property / attribute or meta item.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($this->existsOnParent($name)) {
            return parent::__get($name);
        }

        // Does not exists in parent.
        else if (isset($this->meta) && ! is_null($key = $this->meta->findItem($name))) {
            $item = $this->meta->get($key);

            return $item->value;
        }
    }

    /**
     * Dynamically set a meta item or model property / attribute.
     *
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        if ($this->existsOnParent($name)) {
            return parent::__set($name, $value);
        }

        // Does not exists in parent.
        else if (! is_null($key = $this->meta->findItem($name))) {
            $item = $this->meta->get($key);

            $item->value = $value;

            return;
        }

        $fields = $this->getMetaFields();

        if (! is_null($key = $fields->findItem($name))) {
            $field = $fields->get($key);

            $this->meta->addItem($name, $value, $field->type);
        } else {
            throw new InvalidArgumentException("Invalid field name. [$name]");
        }
    }

    /**
     * Dynamically unset a meta item or model property / attribute.
     *
     * @param $name
     */
    public function __unset($name)
    {
        if ($this->existsOnParent($name)) {
            parent::__unset($name);
        }

        // Does not exists in parent.
        else if (! is_null($key = $this->meta->findItem($name))) {
            $this->meta->forget($key);
        }
    }
}
