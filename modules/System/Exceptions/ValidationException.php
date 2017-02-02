<?php

namespace System\Exceptions;


class ValidationException extends \Exception
{
    /**
     * @var mixed
     */
    protected $errors;


    /**
     * @param string $message
     * @param mixed  $errors
     */
    function __construct($message, $errors)
    {
        $this->errors = $errors;

        //
        parent::__construct($message);
    }

    /**
     * Get the form validation errors
     *
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

}
