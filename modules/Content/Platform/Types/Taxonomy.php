<?php

namespace Modules\Content\Platform\Types;

use Nova\Support\Arr;
use Nova\Support\Str;

use Modules\Content\Platform\TaxonomyManager;


abstract class Taxonomy
{
    /**
     * @var \Modules\Content\Platform\TaxonomyManager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $name;

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


    public function __construct(TaxonomyManager $manager, array $options)
    {
        $this->manager = $manager;

        //
        unset($options['manager'], $options['labels']);

        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function name()
    {
        return $this->name;
    }

    public function model()
    {
        return $this->model;
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

    public function hasLabel($name)
    {
        $label = $this->label($name);

        return ! is_null($label);
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

    public function slug()
    {
        return Arr::get($this->rewrite, 'slug');
    }
}
