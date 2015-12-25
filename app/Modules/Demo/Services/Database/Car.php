<?php

namespace App\Modules\Demo\Services\Database;

use Nova\Database\Service;


class Car extends Service
{
    public function __construct() {
        $this->table       = "car";
        $this->primaryKeys = array("carid");
        $this->fetchMethod = \PDO::FETCH_CLASS;
        $this->fetchClass  = '\App\Modules\Demo\Models\Entities\Car';
    }


    public function getAll()
    {
        return $this->read("SELECT * FROM " . DB_PREFIX . "car");
    }

}
