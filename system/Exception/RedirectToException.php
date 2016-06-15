<?php
/**
 * RedirectToException - Implements a simple Redirect Exception.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Exception;

/**
 * RedirectToException
 *
 */
class RedirectToException extends \RuntimeException
{
    private $statusCode;
    private $url;

    public function __construct($url = null, $statusCode = 302, $message = null, \Exception $previous = null, $code = 0)
    {
        $this->statusCode = $statusCode;
        $this->url = $url;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getUrl()
    {
        return $this->url;
    }
}

