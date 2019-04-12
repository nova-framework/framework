<?php

namespace Modules\Content\Platform\Types\Taxonomies;

use Modules\Content\Platform\Types\Taxonomy;


class Tag extends Taxonomy
{
    /**
     * @var string
     */
    protected $name = 'tag';

    /**
     * @var string
     */
    protected $model = 'Modules\Content\Models\Tag';

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
    protected $rewrite = array(
        'slug' => 'tags'
    );


    /**
     * @return string
     */
    public function description()
    {
        return __d('content', 'A type of non-hierarchical taxonomy.');
    }

    /**
     * @return array
     */
    public static function labels()
    {
        return array(
            'name'        => __d('content', 'Tag'),
            'title'       => __d('content', 'Tags'),

            'searchItems' => __d('content', 'Search Tags'),
            'allItems'    => __d('content', 'All Tag'),

            'parentItem'      => null,
            'parentItemColon' => null,

            'editItem'    => __d('content', 'Edit Tag'),
            'updateItem'  => __d('content', 'Update Tag'),
            'deleteItem'  => __d('content', 'Delete Tag'),
            'addNewItem'  => __d('content', 'Add New Tag'),
            'newItemName' => __d('content', 'New Tag Name'),

            'menuName'    => __d('content', 'Tags'),
        );
    }
}
