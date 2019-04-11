<?php

namespace Modules\Content\Platform\Types;

use Modules\Content\Platform\Types\Content;
use Modules\Content\Platform\PostManager;


abstract class Post extends Content
{
    /**
     * @var string
     */
    protected $view;

    /**
     * @var bool
     */
    protected $hasArchive = true;


    public function __construct(PostManager $manager, array $options)
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
