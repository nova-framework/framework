<?php

namespace App\Modules\Demo\Models\Entities;

use Nova\ORM\Annotation\Column;
use Nova\ORM\Annotation\Table;
use Nova\ORM\Entity;

/**
 * Class Car
 * @package App\Modules\Demo\Models\Entities
 *
 * @Table(name="car")
 */
class Car extends Entity
{
    /**
     * @var int
     * @Column(name="carid")
     */
    public $carid;
    public $make;
    public $model;
    public $costs;
}
