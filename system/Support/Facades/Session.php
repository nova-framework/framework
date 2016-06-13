<?php

namespace Support\Facades;

use Support\Facades\Facade;


/**
 * @see \Session\SessionManager
 * @see \Session\Store
 */
class Session extends Facade
{
    /**
     * Return the Application instance.
     *
     * @return \Pagination\Factory
     */
    public static function instance()
    {
        $accessor = static::getFacadeAccessor();

        return static::resolveFacadeInstance($accessor);
    }

    /**
     * Flash a array containing a message to the session.
     *
     * @param string $message
     * @param string $type
     *
     * @return void
     */
    public static function pushStatus($message, $type = 'success')
    {
        $instance = static::instance();

        $status = array('type' => $type, 'text' => $message);

        // Push the status on Session.
        $instance->push('status', $status);
    }

    /**
     * Display the one time Messages, then clear them from the Session.
     *
     * @param  string $name default Session name
     *
     * @return string
     */
    public static function getMessages()
    {
        $instance = static::instance();

        if (! $instance->has('status')) {
            return null;
        }

        // Pull the Message from the Session Store.
        $messages = $instance->remove('status');

        //
        $content = null;

        foreach ($messages as $message) {
            $content .= static::createMessage($message);
        }

        return $content;
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'session.store'; }

}
