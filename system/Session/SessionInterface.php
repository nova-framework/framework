<?php

namespace Session;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface as BaseSessionInterface;


interface SessionInterface extends BaseSessionInterface
{
    /**
     * Get the session handler instance.
     *
     * @return \SessionHandlerInterface
     */
    public function getHandler();
}
