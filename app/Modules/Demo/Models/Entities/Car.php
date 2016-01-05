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
     * @Column(name="carid", primary=true, type="int", autoIncrement=true)
     */
    public $carid;

    /**
     * @var string
     * @Column(name="make", type="string")
     */
    public $make;

    /**
     * @var string
     * @Column(name="model", type="string")
     */
    public $model;

    /**
     * @var double
     * @Column(name="costs", type="double")
     */
    public $costs;
}
