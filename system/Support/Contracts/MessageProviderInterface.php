<?php

namespace Nova\Support\Contracts;


interface MessageProviderInterface
{
    /**
     * Get the messages for the instance.
     *
     * @return \Nova\Support\MessageBag
     */
    public function getMessageBag();
}
