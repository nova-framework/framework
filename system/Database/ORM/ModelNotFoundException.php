<?php

namespace Nova\Database\ORM;


class ModelNotFoundException extends \RuntimeException
{
    /**
     * Name of the affected ORM model.
     *
     * @var string
     */
    protected $model;

    /**
     * Set the affected ORM model.
     *
     * @param  string   $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        $this->message = "No query results for model [{$model}].";

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
