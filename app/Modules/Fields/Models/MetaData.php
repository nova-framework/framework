<?php

namespace App\Modules\Fields\Models;

use Nova\Database\ORM\Model;
use Nova\Support\Facades\App;

use App\Modules\Fields\Support\MetaCollection;
use App\Modules\Fields\Support\FieldRegistry;

use DateTime;


class MetaData extends Model
{
    /**
     * @var App\Modules\Fields\Meta\Type
     */
    protected $fieldInstance;

    /**
     * @var array
     */
    protected $fillable = array('key', 'value');


    /**
     * Get the value type registry.
     *
     * @return \App\Modules\Fields\Support\FieldRegistry
     */
    protected function getFieldRegistry()
    {
        return App::make(FieldRegistry::class);
    }

    /**
     * Get the models value type instance.
     *
     * @return \App\Modules\Fields\Fields\Field
     */
    public function getField()
    {
        if (isset($this->fieldInstance)) {
            return $this->fieldInstance;
        }

        $fieldClass = $this->getFieldRegistry()->get($this->type);

        return $this->fieldInstance = new $fieldClass($this);
    }

    /**
     * Parse and get the value attribute.
     *
     * @return mixed
     */
    public function getValueAttribute()
    {
        return $this->getField()->get();
    }

    /**
     * Parse and set the value attribute.
     *
     * @param mixed $value
     */
    public function setValueAttribute($value)
    {
        if (! isset($this->attributes['type'])) {
            $field = $this->getFieldRegistry()->findFieldFor($value);

            $this->attributes['type'] = $field->getClass();
        }

        return $this->getField()->set($value);
    }

    /**
     * Get the value attribute by-passing any accessors.
     *
     * @return mixed
     */
    public function getRawValue()
    {
        return $this->attributes['value'];
    }

    /**
     * Set the value attribute by-passing the mutators.
     *
     * @param mixed $value
     */
    public function setRawValue($value)
    {
        $this->attributes['value'] = $value;
    }

    /**
     * Create a new ORM Collection instance.
     *
     * @param  array  $models
     * @return \App\Modules\Fields\Support\MetaCollection
     */
    public function newCollection(array $models = array())
    {
        return new MetaCollection($models);
    }

    /**
     * Get the string value of the meta item.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getField()->toString();
    }
}
