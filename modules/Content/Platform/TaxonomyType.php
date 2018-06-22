<?php

namespace Modules\Content\Platform;

use Nova\Support\Arr;
use Nova\Support\Str;


class TaxonomyType
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $labels = array();

    /**
     * @var bool
     */
    protected $hierarchical = false;

    /**
     * @var array
     */
    protected $rewrite = array();


    public function __construct($name, array $config)
    {
        $this->name = $name;

        // Initialize the properties from config.
        $this->label = Arr::get($config, 'label');

        $this->description = Arr::get($config, 'description');

        $this->labels = Arr::get($config, 'labels', array());

        $this->hierarchical = (bool) Arr::get($config, 'hierarchical', false);

        $this->rewrite = Arr::get($config, 'rewrite', array());
    }

    public function name()
    {
        return $this->name;
    }

    public function label($name = null, $default = null)
    {
        if (is_null($name)) {
            return $this->label;
        }

        $name = Str::camel($name);

        return Arr::get($this->labels, $name, $default ?: Str::title($name));
    }

    public function hasLabel($name)
    {
        return Arr::has($this->labels, $name);
    }

    public function description()
    {
        return $this->description;
    }

    public function hierarchical()
    {
        return $this->hierarchical;
    }

    public function slug()
    {
        return Arr::get($this->rewrite, 'slug');
    }
}
