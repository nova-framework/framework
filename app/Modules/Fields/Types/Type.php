<?php

namespace App\Modules\Fields\Types;

use Nova\Http\Request;
use Nova\Support\Facades\View;

use App\Modules\Fields\Models\Field;
use App\Modules\Fields\Models\MetaData as MetaItem;


abstract class Type
{
    /**
     * MetaData model instance.
     *
     * @var \App\Modules\Fields\Models\MetaData
     */
    protected $model;

    /**
     * Field model instance.
     *
     * @var \App\Modules\Fields\Models\Field
     */
    protected $field;

    /**
     * The partial View used for editor rendering.
     *
     * @var string
     */
    protected $view = 'Editor/Default';


    /**
     * Constructor.
     *
     * @param \App\Modules\Fields\Models\MetaData|null $model
     */
    public function __construct(MetaItem $model = null)
    {
        $this->model = $model;
    }

    /**
     * Set the Field model instance.
     *
     * @param \App\Modules\Fields\Models\Field  $field
     */
    public function setField(Field $field)
    {
        $this->field = $field;

        return $this;
    }

    public function renderForEditor(Request $request)
    {
        $field = $this->field;

        // Calculate the current value.
        $default = isset($this->model) ? $this->model->value : null;

        $value = $request->old($field->key, $default);

        return View::make($this->view, compact('field', 'value'), 'Fields')->render();
    }

    /**
     * Gets the model instance.
     *
     * @return \App\Modules\Fields\Models\MetaData|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Gets the model instance.
     *
     * @return \App\Modules\Fields\Models\Field|null
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Parse & return the meta item value.
     *
     * @return mixed
     */
    public function get()
    {
        if (isset($this->model)) {
            return $this->model->getRawValue();
        }
    }

    /**
     * Parse & set the meta item value.
     *
     * @param mixed $value
     */
    public function set($value)
    {
        if (isset($this->model)) {
            $this->model->setRawValue($value);
        }
    }

    /**
     * Assertain whether we can handle the type of variable passed.
     *
     * @param  mixed  $value
     * @return bool
     */
    abstract public function isType($value);

    /**
     * Get the types class name.
     *
     * @return string
     */
    public function getClass()
    {
        return get_class($this);
    }

    /**
     * Output value to string.
     *
     * @return string
     */
    public function toString()
    {
        return serialize($this->get());
    }

    /**
     * Output value to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
