<?php

namespace Support\Facades;

use Support\Facades\Facade;
use Support\Facades\Template;
use Support\MessageBag;


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
     * Display a one time Message, then clear it from the Session.
     *
     * @param  string $name default Session name
     *
     * @return string
     */
    public static function message($name = null)
    {
        $instance = static::instance();

        if(is_null($name)) {
            foreach (array('info', 'success', 'warning', 'danger') as $key) {
                if ($instance->has($key)) {
                    $name = $key;

                    break;
                }
            }
        }

        if (! is_null($name) && $instance->has($name)) {
            // Pull the Message from the Session Store.
            $message = $instance->remove($name);

            return static::createMessage($message, $name);
        }
    }

    protected static function createMessage($message, $name = null)
    {
        if(is_array($message)) {
            $type    = $message['type'];
            $message = $message['text'];
        } else {
            $type = $name;
        }

        // Adjust the alert Type.
        switch ($type) {
            case 'info':
            case 'success':
            case 'warning':
            case 'danger':
                break;

            default:
                $type = 'success';

                break;
        }

        // Handle the multiple line messages.
        if($message instanceof MessageBag) {
            $message = $message->all();
        }

        // Handle the array messages.
        if (is_array($message)) {
            if (count($message) > 1) {
                $message = '<ul><li>' .implode('</li><li>', $message) .'</li></ul>';
            } else if(! empty($message)) {
                $message = array_shift($message);
            } else {
                // An empty array?
                $message = '';
            }
        }

        // Fetch the associated Template Fragment and return the result.
        return Template::make('message', compact('type', 'message'), TEMPLATE)->render();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'session'; }

}
