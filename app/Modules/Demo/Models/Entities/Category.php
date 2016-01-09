<?php

namespace App\Modules\Demo\Models\Entities;

use Nova\ORM\Annotation\Column;
use Nova\ORM\Annotation\Table;
use Nova\ORM\Entity;

/**
 * DataObject/Entity Category
 *
 * @Table(name="category")
 */
class Category extends Entity
{
    /**
     * @var int
     * @Column(name="categoryid", primary=true, type="int", autoIncrement=true)
     */
    public $categoryid;
}
