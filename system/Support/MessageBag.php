<?php
/**
 * MessageBag - Implements a Bag for Messages.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support;


class MessageBag {

    /**
     * All of the registered messages.
     *
     * @var array
     */
    protected $messages = array();

    /**
     * Default format for message output.
     *
     * @var string
     */
    protected $format = ':message';

    /**
     * Create a new Message Bag instance.
     *
     * @param  array  $messages
     * @return void
     */
    public function __construct(array $messages = array())
    {
        foreach ($messages as $key => $value) {
            $this->messages[$key] = (array) $value;
        }
    }

    /**
     * Add a message to the bag.
     *
     * @param  string  $key
     * @param  string  $message
     * @return \Support\MessageBag
     */
    public function add($key, $message)
    {
        if ($this->isUnique($key, $message)) {
            $this->messages[$key][] = $message;
        }

        return $this;
    }

    /**
     * Merge a new array of messages into the bag.
     *
     * @param  array  $messages
     * @return \Support\MessageBag
     */
    public function merge($messages)
    {
        $this->messages = array_merge_recursive($this->messages, $messages);

        return $this;
    }

    /**
     * Determine if a key and message combination already exists.
     *
     * @param  string  $key
     * @param  string  $message
     * @return bool
     */
    protected function isUnique($key, $message)
    {
        $messages = (array) $this->messages;

        return ! isset($messages[$key]) || ! in_array($message, $messages[$key]);
    }

    /**
     * Determine if messages exist for a given key.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key = null)
    {
        return $this->first($key) !== '';
    }

    /**
     * Get the first message from the bag for a given key.
     *
     * @param  string  $key
     * @param  string  $format
     * @return string
     */
    public function first($key = null, $format = null)
    {
        $messages = is_null($key) ? $this->all($format) : $this->get($key, $format);

        return (count($messages) > 0) ? $messages[0] : '';
    }

    /**
     * Get all of the messages from the bag for a given key.
     *
     * @param  string  $key
     * @param  string  $format
     * @return array
     */
    public function get($key, $format = null)
    {
        $format = $this->checkFormat($format);

        if (array_key_exists($key, $this->messages)) {
            return $this->transform($this->messages[$key], $format, $key);
        }

        return array();
    }

    /**
     * Get all of the messages for every key in the bag.
     *
     * @param  string  $format
     * @return array
     */
    public function all($format = null)
    {
        $format = $this->checkFormat($format);

        $all = array();

        foreach ($this->messages as $key => $messages) {
            $all = array_merge($all, $this->transform($messages, $format, $key));
        }

        return $all;
    }

    /**
     * Format an array of messages.
     *
     * @param  array   $messages
     * @param  string  $format
     * @param  string  $messageKey
     * @return array
     */
    protected function transform($messages, $format, $messageKey)
    {
        $messages = (array) $messages;

        foreach ($messages as $key => &$message) {
            $replace = array(':message', ':key');

            $message = str_replace($replace, array($message, $messageKey), $format);
        }

        return $messages;
    }

    /**
     * Get the appropriate format based on the given format.
     *
     * @param  string  $format
     * @return string
     */
    protected function checkFormat($format)
    {
        return ($format === null) ? $this->format : $format;
    }

    /**
     * Get the raw messages in the container.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Get the messages for the instance.
     *
     * @return \Support\MessageBag
     */
    public function getMessageBag()
    {
        return $this;
    }

    /**
     * Get the default message format.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set the default message format.
     *
     * @param  string  $format
     * @return \Support\MessageBag
     */
    public function setFormat($format = ':message')
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Determine if the message bag has any messages.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return ! $this->any();
    }

    /**
     * Determine if the message bag has any messages.
     *
     * @return bool
     */
    public function any()
    {
        return ($this->count() > 0);
    }

    /**
     * Get the number of messages in the container.
     *
     * @return int
     */
    public function count()
    {
        return (count($this->messages, COUNT_RECURSIVE) - count($this->messages));
    }

    /**
     * Clear the messages array.
     *
     * @return void
     */
    public function clear()
    {
        $this->messages = array();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getMessages();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert the message bag to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
