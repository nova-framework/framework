<?php

namespace Modules\Content\Models;

use Nova\Support\Arr;

use Modules\Content\Models\Post;


class MenuItem extends Post
{
    /**
     * @var string
     */
    protected $postType = 'nav_menu_item';

    /**
     * @var array
     */
    private $instanceRelations = array(
        'post'     => 'Modules\Content\Models\Post',
        'page'     => 'Modules\Content\Models\Page',
        'custom'   => 'Modules\Content\Models\CustomLink',
        'category' => 'Modules\Content\Models\Taxonomy',
    );

    /**
     * @return Post|Page|CustomLink|Taxonomy
     */
    public function parent()
    {
        if (! is_null($className = $this->getClassName())) {
            return with(new $className)->newQuery()->find($this->meta->menu_item_menu_item_parent);
        }
    }

    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany('Modules\Content\Models\MenuItem', 'parent_id');
    }

    /**
     * @return Post|Page|CustomLink|Taxonomy
     */
    public function instance()
    {
        if (! is_null($className = $this->getClassName())) {
            return with(new $className)->newQuery()->find($this->meta->menu_item_object_id);
        }
    }

    /**
     * @return string
     */
    private function getClassName()
    {
        return Arr::get($this->instanceRelations, $this->meta->menu_item_object);
    }
}
