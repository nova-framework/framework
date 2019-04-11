<?php

namespace Modules\Content\Platform;

use Nova\Support\Arr;
use Nova\Support\Str;

use Modules\Content\Platform\ContentManager;
use Modules\Content\Platform\PostManager;


abstract class ContentType
{
    /**
     * @var \Modules\Content\Platform\ContentManager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var bool
     */
    protected $hidden = false;

    /**
     * @var bool
     */
    protected $public = true;

    /**
     * @var bool
     */
    protected $hierarchical = false;

    /**
     * @var array
     */
    protected $rewrite = array();

    /**
     * @var array
     */
    protected $labels = array();


    public function __construct(ContentManager $manager, array $options)
    {
        $this->manager = $manager;

        //
        unset($options['manager'], $options['labels']);

        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }
    }

    abstract public function description();

    abstract public function labels();

    public function label($name, $default = null)
    {
        $locale = $this->manager->getCurrentLocale();

        if (! isset($this->labels[$locale])) {
            $this->labels[$locale] = $this->labels();
        }

        $key = sprintf('%s.%s', $locale, Str::camel($name));

        return Arr::get($this->labels, $key, $default);
    }

    public function name()
    {
        return $this->name;
    }

    public function model()
    {
        return $this->model;
    }

    public function isHidden()
    {
        return $this->hidden;
    }

    public function isPublic()
    {
        return $this->public;
    }

    public function isHierarchical()
    {
        return $this->hierarchical;
    }

    public function rewrite($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->rewrite;
        }

        return Arr::get($this->rewrite, $key, $default);
    }

    public function slug($plural = true)
    {
        if ($plural) {
            return $this->rewrite('slug');
        }

        return Str::snake($this->name(), '-');
    }
}
