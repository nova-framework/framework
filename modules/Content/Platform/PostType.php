<?php

namespace Modules\Content\Platform;

use Nova\Support\Arr;
use Nova\Support\Str;

use Modules\Content\Models\Post;

use InvalidArgumentException;


class PostType
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var string
     */
    protected $view;

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
    protected $public = true;

    /**
     * @var bool
     */
    protected $hierarchical = false;

    /**
     * @var bool
     */
    protected $hasArchive = true;

    /**
     * @var array
     */
    protected $rewrite = array();

    /**
     * @var PostType[]
     */
    protected static $types = array();


    public function __construct($name, array $config)
    {
        $this->name = $name;

        if (is_null($model = Arr::get($config, 'model'))) {
            throw new InvalidArgumentException('Model not specified');
        }

        $this->model = $model;

        // Initialize the properties from config.
        $this->view = Arr::get($config, 'view', 'Modules/Content::Content/Post');

        $this->label = Arr::get($config, 'label');

        $this->description = Arr::get($config, 'description');

        $this->labels = Arr::get($config, 'labels', array());

        $this->hierarchical = (bool) Arr::get($config, 'hierarchical', false);

        $this->public = (bool) Arr::get($config, 'public', false);

        $this->hasArchive = (bool) Arr::get($config, 'hasArchive', true);

        $this->rewrite = Arr::get($config, 'rewrite', array());
    }

    public function name()
    {
        return $this->name;
    }

    public function model()
    {
        return $this->model;
    }

    public function view()
    {
        return $this->view;
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

    public function public()
    {
        return $this->public;
    }

    public function hierarchical()
    {
        return $this->hierarchical;
    }

    public function hasArchive()
    {
        return $this->hasArchive;
    }

    public function rewrite($key = null)
    {
        if (! is_null($key)) {
            return Arr::get($this->rewrite, $key);
        }

        return $this->rewrite;
    }

    public function slug()
    {
        return Arr::get($this->rewrite, 'slug');
    }
}
