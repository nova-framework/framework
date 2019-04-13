<?php

namespace Modules\Content\Platform;

use Nova\Container\Container;
use Nova\Support\Arr;

use Modules\Content\Platform\ContentType;

use Closure;
use InvalidArgumentException;


class ContentLabelManager
{
    /**
     * @var \Nova\Container\Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $types = array();

    /**
     * @var array
     */
    protected $labels = array();


    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function register($type, $callback)
    {
        if ((! $callback instanceof Closure) && ! is_subclass_of($callback, $baseClass = ContentType::class)) {
            throw new InvalidArgumentException("The callback must be a a closure or a subclass of [{$baseClass}]");
        }

        //
        else if (isset($this->types[$type])) {
            throw new InvalidArgumentException("The Content type [{$type}] is already registered");
        }

        $this->types[$type] = $callback;

        return $this;
    }

    public function forget($type)
    {
        unset($this->types[$type]);
    }

    public function get($type, $name, $default = null)
    {
        $key = sprintf('%s.%s', $type, $this->getCurrentLocale());

        if (! Arr::has($this->labels, $key) && ! is_null($callback = Arr::get($this->types, $type))) {
            if ($callback instanceof Closure) {
                $labels = call_user_func($callback);
            } else {
                $labels = forward_static_call(array($callback, 'labels'));
            }

            Arr::set($this->labels, $key, (array) $labels);
        }

        // The labels are already cached locally or the Content Type is not registered.
        else {
            $labels = Arr::get($this->labels, $key, array());
        }

        return Arr::get($labels, $name, $default);
    }

    protected function getCurrentLocale()
    {
        $languageManager = $this->container['language'];

        return $languageManager->getLocale();
    }
}
