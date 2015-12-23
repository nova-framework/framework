<?php


namespace App\Services\Database;

use Nova\Database\Manager;
use Nova\Database\Service\SQLite;

class CarLite extends SQLite
{
    public function __construct()
    {
        parent::__construct(Manager::getEngine('sqlite'));

        $this->table = "car";
        $this->primaryKeys = array("carid");
        $this->fetchMethod = \PDO::FETCH_CLASS;
        $this->fetchClass = '\App\Models\Entities\CarLite';
    }


    public function getAll()
    {
        return $this->read("SELECT * FROM " . DB_PREFIX . "car");
    }
}
