<?php

namespace App\Modules\Fields\Models;

use Nova\Database\ORM\Model;
use Nova\Support\Facades\App;

use App\Modules\Fields\Models\Collection\MetaCollection;
use App\Modules\Fields\Types\Registry as TypeRegistry;

use DateTime;


class MetaData extends Model
{
    /**
     * @var App\Modules\Fields\Meta\Type
     */
    protected $typeInstance;

    /**
     * @var array
     */
    protected $fillable = array('key', 'value');


    /**
     * Get the value type registry.
     *
     * @return \App\Modules\Fields\Types\Registry
     */
    protected function getTypeRegistry()
    {
        return App::make(TypeRegistry::class);
    }

    /**
     * Get the models value type instance.
     *
     * @return \App\Modules\Fields\Fields\Field
     */
    public function getTypeInstance()
    {
        if (isset($this->typeInstance)) {
            return $this->typeInstance;
        }

        $fieldClass = $this->getTypeRegistry()->get($this->type);

        return $this->typeInstance = new $fieldClass($this);
    }

    /**
     * Parse and get the value attribute.
     *
     * @return mixed
     */
    public function getValueAttribute()
    {
        return $this->getTypeInstance()->get();
    }

    /**
     * Parse and set the value attribute.
     *
     * @param mixed $value
     * @param mixed $type
     */
    public function setValueAttribute($value, $type = null)
    {
        if (is_null($type) && ! isset($this->attributes['type'])) {
            $field = $this->getTypeRegistry()->findTypeFor($value);

            $this->attributes['type'] = $field->getClass();
        } else if (isset($type)) {
            $this->attributes['type'] = $type;
        }

        return $this->getTypeInstance()->set($value);
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
     * Gets a rendered form of the value.
     *
     * @return string
     */
    public function render()
    {
        return $this->getTypeInstance()->render();
    }

    /**
     * Get the string value of the meta item.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTypeInstance()->toString();
    }
}
