<?php

namespace App\Modules\Fields\Types;

use Nova\Http\Request;
use Nova\Support\Facades\View;

use App\Modules\Fields\Models\MetaData as MetaItem;


abstract class Type
{
    /**
     * The type handled by this Type class.
     *
     * @var string|null
     */
    protected $type;

    /**
     * MetaData model instance.
     *
     * @var \App\Modules\Fields\Models\MetaData
     */
    protected $model;

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
     * Execute the cleanup when MetaData instance is saved or deleted.
     *
     * @return string
     */
    public function cleanup($force = false)
    {
        //
    }

    /**
     * Gets a rendered form of the value.
     *
     * @return string
     */
    public function render()
    {
        return $this->get();
    }

    /**
     * Gets the type handled by this Type class.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
     * Gets the View used for rendering the editor.
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
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
