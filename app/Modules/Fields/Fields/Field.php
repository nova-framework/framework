<?php

namespace App\Modules\Fields\Fields;

use Nova\Http\Request;
use Nova\Support\Facades\View;

use App\Modules\Fields\Models\Field as FieldItem;
use App\Modules\Fields\Models\MetaData as MetaItem;


abstract class Field
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
    protected $item;

    /**
     * The partial View used for rendering.
     *
     * @var string
     */
    protected $view = 'Fields/Editor/Default';


    /**
     * Constructor.
     *
     * @param \App\Modules\Fields\Models\MetaData|null $model
     */
    public function __construct(MetaItem $model = null)
    {
        $this->model = $model;
    }

    public function setItem(FieldItem $item)
    {
        $this->item = $item;

        return $this;
    }

    public function renderForEditor(Request $request)
    {
        $item = $this->item;

        $value = isset($this->model) ? $this->model->value : '';

        return View::make($this->view, compact('request', 'item', 'value'), 'Fields')
            ->with('value', $request->old($item->key, $value))
            ->render();
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
    abstract public function isField($value);

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