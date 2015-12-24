<?php


namespace App\Services\Database;

use Nova\Database\Service;

class Car extends Service
{
    public function __construct() {
        parent::__construct();

        $this->table = "car";
        $this->primaryKeys = array("carid");
        $this->fetchMethod = \PDO::FETCH_CLASS;
        $this->fetchClass = '\App\Models\Entities\Car';
    }


    public function getAll()
    {
        return $this->read("SELECT * FROM " . DB_PREFIX . "car");
    }
}
