<?php

namespace Modules\Content\Platform\Types\Posts;

use Modules\Content\Platform\Types\Post as BasePost;


class CustomLink extends BasePost
{
    /**
     * @var string
     */
    protected $name = 'custom';

    /**
     * @var string
     */
    protected $model = 'Modules\Content\Models\CustomLink';

    /**
     * @var string
     */
    protected $view = 'Modules/Content::Content/CustomLink';

    /**
     * @var bool
     */
    protected $hidden = true;

    /**
     * @var bool
     */
    protected $public = false;

    /**
     * @var bool
     */
    protected $hierarchical = false;

    /**
     * @var bool
     */
    protected $hasArchive = false;

    /**
     * @var array
     */
    protected $rewrite = array(
        'item'  => 'custom-link',
        'items' => 'custom-links'
    );


    /**
     * @return string
     */
    public function description()
    {
        return __d('content', 'A menu item which is custom defined.');
    }

    /**
     * @return array
     */
    public static function labels()
    {
        return array(
            'name'        => __d('content', 'Custom Link'),
            'title'       => __d('content', 'Custom Links'),

            'searchItems' => __d('content', 'Search Custom Links'),
            'allItems'    => __d('content', 'All Custom Links'),
            'notFound'    => __d('content', 'No custom links found'),

            'parentItem'      => null,
            'parentItemColon' => null,

            'addNew'      => __d('content', 'Add New'),
            'addNewItem'  => __d('content', 'Add New Custom Link'),
            'editItem'    => __d('content', 'Edit Custom Link'),
            'updateItem'  => __d('content', 'Update Custom Link'),
            'deleteItem'  => __d('content', 'Delete Custom Link'),
            'newItem'     => __d('content', 'New Custom Link'),
            'viewItem'    => __d('content', 'View Custom Link'),

            'menuName'    => __d('content', 'Custom Links'),
        );
    }
}
