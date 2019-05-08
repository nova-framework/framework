<?php

namespace Modules\Content\Platform\ContentTypes;

use Modules\Content\Platform\ContentType;
use Modules\Content\Platform\PostTypeManager;


abstract class Post extends ContentType
{
    /**
     * @var string
     */
    protected $view;

    /**
     * @var bool
     */
    protected $hasArchive = true;


    public function __construct(PostTypeManager $manager, array $options)
    {
        parent::__construct($manager, $options);
    }

    public function view()
    {
        return $this->view;
    }

    public function hasArchive()
    {
        return $this->hasArchive;
    }
}
