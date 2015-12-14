<?php


namespace App\Models\Entities;

use Smvc\Core\Database\Entity;

class Car extends Entity
{
    public $carid;
    public $makeid;
    public $model;
    public $type;
    public $costs;
}