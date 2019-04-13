<?php

namespace Modules\Content\Platform;

use Nova\Container\Container;
use Nova\Support\Arr;
use Nova\Support\Str;

use Modules\Content\Platform\ContentType;

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

    public function register($type, $className)
    {
        if (! is_subclass_of($className, $baseClass = ContentType::class)) {
            dd($className, $baseClass);

            throw new InvalidArgumentException("The Content Type class must be a subclass of [{$baseClass}]");
        }

        //
        else if (isset($this->types[$type])) {
            throw new InvalidArgumentException("The Content type [{$type}] is already registered");
        }

        $this->types[$type] = $className;

        return $this;
    }

    public function forget($type)
    {
        unset($this->types[$type]);
    }

    public function get($type, $name, $default = null)
    {
        $key = sprintf('%s.%s', $type, $this->getCurrentLocale());

        if (! Arr::has($this->labels, $key) && isset($this->types[$type])) {
            $className = $this->types[$type];

            $labels = (array) forward_static_call(array($className, 'labels'));

            Arr::set($this->labels, $key, $labels);
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
