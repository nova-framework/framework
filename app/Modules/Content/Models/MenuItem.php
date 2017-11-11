<?php

namespace App\Modules\Content\Models;

use Nova\Support\Arr;

use App\Modules\Content\Models\Post;


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
        'post'     => 'App\Modules\Content\Models\Post',
        'page'     => 'App\Modules\Content\Models\Page',
        'custom'   => 'App\Modules\Content\Models\CustomLink',
        'category' => 'App\Modules\Content\Models\Taxonomy',
    );

    /**
     * @return Post|Page|CustomLink|Taxonomy
     */
    public function parent()
    {
        if (! is_null($className = $this->getClassName())) {
            return with(new $className)->newQuery()->find($this->meta->_menu_item_menu_item_parent);
        }
    }

    /**
     * @return Post|Page|CustomLink|Taxonomy
     */
    public function instance()
    {
        if (! is_null($className = $this->getClassName())) {
            return with(new $className)->newQuery()->find($this->meta->_menu_item_object_id);
        }
    }

    /**
     * @return string
     */
    private function getClassName()
    {
        return Arr::get($this->instanceRelations, $this->meta->_menu_item_object);
    }
}
