<?php

namespace App\Modules\Fields\Traits;

use Nova\Database\ORM\Relations\HasMany;
use Nova\Database\ORM\Collection;
use Nova\Support\Facades\Schema;
use Nova\Support\Collection as BaseCollection;
use Nova\Support\Str;

use App\Modules\Fields\Models\Builder\MetaBuilder;
use App\Modules\Fields\Models\MetaData;


trait MetableTrait
{
    /**
     * Model table columns.
     *
     * @var array
     */
    public static $tableColumns = array();


    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootMetableTrait()
    {
        static::observeSaveAndCascade();

        static::observeDeleteAndCascade();
    }

    public function meta()
    {
        $metaTable = $this->getMetaTable();

        with($model = new MetaData())->setTable($metaTable);

        return new HasMany($model->newQuery(), $this, $this->getMetaKeyName(), $this->getKeyName());
    }

    /**
     * @param \Nova\Database\Query\Builder $query
     * @return \App\Modules\Fields\Database\ORM\Builder
     */
    public function newQueryBuilder($query)
    {
        return new MetaBuilder($query);
    }

    /**
     * Determine if the model or given attribute(s) have been modified.
     *
     * @param  array|string|null  $attributes
     * @return bool
     */
    public function isDirty($attributes = null)
    {
        if (parent::isDirty($attributes)) {
            return true;
        } else if (! isset($this->meta)) {
            return false;
        }

        foreach ($this->meta as $item) {
            if ($item->isDirty($attributes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Observes the model and saves dirty meta data on save.
     *
     * @return void
     */
    public static function observeSaveAndCascade()
    {
        static::saved(function ($model)
        {
            if (! isset($model->meta)) {
                return;
            }

            //
            // Remove any keys not present in the collection.

            $keyName = with(new MetaData())->getKeyName();

            $ids = array_diff($model->meta->originalModelKeys(), $model->meta->modelKeys());

            if (! empty($ids)) {
                $model->meta()->whereIn($keyName, $ids)->delete();
            }

            //
            // Save dirty meta items.

            $metaTable = $model->getMetaTable();

            foreach ($model->meta as $item) {
                if (! $item->isDirty()) {
                    continue;
                }

                $item->setTable($metaTable);

                if ($item->exists) {
                    $item->save();
                } else {
                    $model->meta()->save($item);
                }
            }
        });
    }

    /**
     * Observes the model and deletes meta entries on delete.
     *
     * @return void
     */
    public static function observeDeleteAndCascade()
    {
        static::deleted(function ($model)
        {
            $model->meta()->delete();
        });
    }

    /**
     * Return the foreign key name for the meta table.
     *
     * @return string
     */
    public function getMetaKeyName()
    {
        return isset($this->metaKeyName) ? $this->metaKeyName : $this->getForeignKey();
    }

    /**
     * Return the table name.
     *
     * @return null
     */
    public function getMetaTable()
    {
        if (isset($this->metaTable)) {
            return $this->metaTable;
        }

        return $this->getTable() .'_meta';
    }

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

        return ! is_null($this->meta->findItem($name));
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

        if (! is_null($key = $this->meta->findItem($name))) {
            $item = $this->meta->get($key);

            return $item->getTypeInstance();
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

        if (! is_null($key = $this->meta->findItem($name))) {
            $item = $this->meta->get($key);

            $item->value = $value;
        } else {
            $this->meta->addItem($name, $value);
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

        // The column is not in parent table.
        else if (! is_null($key = $this->meta->findItem($name))) {
            $this->meta->forget($key);
        }
    }
}
