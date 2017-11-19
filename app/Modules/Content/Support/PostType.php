<?php

namespace App\Modules\Content\Support;

use Nova\Support\Facades\Config;
use Nova\Support\Arr;
use Nova\Support\Str;

use App\Modules\Content\Models\Post;

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


    public function __construct($name)
    {
        if (is_null($config = Config::get('content::postTypes.' .$name))) {
            throw new InvalidArgumentException('Invalid Post type specified');
        }

        $this->name = $name;

        if (is_null($model = Arr::get($config, 'model'))) {
            throw new InvalidArgumentException('Model not specified');
        }

        $this->model = $model;

        // Initialize the properties from config.
        $this->label = Arr::get($config, 'label');

        $this->description = Arr::get($config, 'description');

        $this->labels = Arr::get($config, 'labels', array());

        $this->hierarchical = (bool) Arr::get($config, 'hierarchical', false);

        $this->hasArchive = (bool) Arr::get($config, 'hasArchive', true);

        $this->rewrite = Arr::get($config, 'rewrite', array());
    }

    public static function make($type)
    {
        if ($type instanceof Post) {
            $type = $post->type;
        }

        if (isset(static::$types[$type])) {
            return static::$types[$type];
        }

        return static::$types[$type] = new static($type);
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

    public function hasArchive()
    {
        return $this->hasArchive;
    }

    public function rewrite()
    {
        return $this->rewrite;
    }
}
