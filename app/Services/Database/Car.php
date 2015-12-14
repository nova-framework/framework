<?php


namespace App\Services\Database;

use Core\Database\Service\MySQLService;

class Car extends MySQLService
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
        return $this->read("* FROM " . PREFIX . "car");
    }
}