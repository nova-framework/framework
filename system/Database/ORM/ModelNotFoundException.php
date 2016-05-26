<?php

namespace Database\ORM;


class ModelNotFoundException extends \RuntimeException
{
    /**
     * Name of the affected ORM Model.
     *
     * @var string
     */
    protected $model;

    /**
     * Set the affected ORM Model.
     *
     * @param  string   $model
     * @return ModelNotFoundException
     */
    public function setModel($model)
    {
        $this->model = $model;

        $this->message = "No query results for Model [{$model}].";

        return $this;
    }

    /**
     * Get the affected ORM model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

}
