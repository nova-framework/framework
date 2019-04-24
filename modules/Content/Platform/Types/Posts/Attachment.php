<?php

namespace Modules\Content\Platform\Types\Posts;

use Modules\Content\Platform\Types\Post as BasePost;


class Attachment extends BasePost
{
    /**
     * @var string
     */
    protected $name = 'attachment';

    /**
     * @var string
     */
    protected $model = 'Modules\Content\Models\Attachment';

    /**
     * @var string
     */
    protected $view = 'Modules/Content::Content/Attachment';

    /**
     * @var bool
     */
    protected $hidden = true;

    /**
     * @var bool
     */
    protected $public = true;

    /**
     * @var bool
     */
    protected $showInMenu = false;

    /**
     * @var bool
     */
    protected $showInNavMenus = false;

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
        'item'  => 'attachment',
        'items' => 'attachments'
    );


    /**
     * @return string
     */
    public function description()
    {
        return __d('content', 'An uploaded file belonging to a Post.');
    }

    /**
     * @return array
     */
    public static function labels()
    {
        return array(
            'name'        => __d('content', 'Attachment'),
            'title'       => __d('content', 'Attachments'),

            'searchItems' => __d('content', 'Search Attachments'),
            'allItems'    => __d('content', 'All Attachments'),
            'notFound'    => __d('content', 'No attachments found'),

            'parentItem'      => null,
            'parentItemColon' => null,

            'addNew'      => __d('content', 'Add New'),
            'addNewItem'  => __d('content', 'Add New Attachment'),
            'editItem'    => __d('content', 'Edit Attachment'),
            'updateItem'  => __d('content', 'Update Attachment'),
            'deleteItem'  => __d('content', 'Delete Attachment'),
            'newItem'     => __d('content', 'New Attachment'),
            'viewItem'    => __d('content', 'View Attachment'),

            'menuName'    => __d('content', 'Attachments'),
        );
    }
}
