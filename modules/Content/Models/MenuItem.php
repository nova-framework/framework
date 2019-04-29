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
    private static $instanceRelations = array(
        //'post'     => 'Modules\Content\Models\Post',
        //'page'     => 'Modules\Content\Models\Page',
        'custom'   => 'Modules\Content\Models\CustomLink',
        //'category' => 'Modules\Content\Models\Taxonomy',
    );


    /**
     * @return void
     */
    public static function registerInstanceRelation($type, $model)
    {
        static::$instanceRelations[$type] = $model;
    }

    /**
     * @return void
     */
    public static function forgetInstanceRelation($type)
    {
        unset(static::$instanceRelations[$type]);
    }

    /**
     * @return array
     */
    public static function getInstanceRelationTypes()
    {
        return array_keys(static::$instanceRelations);
    }

    /**
     * @return Post|Page|CustomLink|Taxonomy
     */
    public function parent()
    {
        if (! is_null($className = $this->getClassName())) {
            $parentId = $this->meta->menu_item_menu_item_parent;

            return with(new $className)->newQuery()->find($parentId);
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
            $objectId = $this->meta->menu_item_object_id;

            return with(new $className)->newQuery()->find($objectId);
        }
    }

    /**
     * @return string
     */
    private function getClassName()
    {
        $type = $this->meta->menu_item_object;

        return Arr::get(static::$instanceRelations, $type);
    }
}
