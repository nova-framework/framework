<?php

namespace App\Modules\Demo\Models\Entities;

use Nova\ORM\Annotation\Column;
use Nova\ORM\Annotation\Table;
use Nova\ORM\Entity;

/**
 * DataObject/Entity Post
 *
 * @Table(name="post")
 */
class Post extends Entity
{
    /**
     * @var int
     * @Column(name="postid", primary=true, type="int", autoIncrement=true)
     */
    public $postid;
}
