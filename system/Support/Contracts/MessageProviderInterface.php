<?php

namespace Support\Contracts;


interface MessageProviderInterface
{
    /**
     * Get the messages for the instance.
     *
     * @return \Support\MessageBag
     */
    public function getMessageBag();
}
