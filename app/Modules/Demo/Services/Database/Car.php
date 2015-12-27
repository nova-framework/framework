<?php

namespace App\Modules\Demo\Services\Database;

use Nova\Database\Service;


class Car extends Service
{
    protected $fetchMethod = \PDO::FETCH_CLASS;
    protected $fetchClass  = '\App\Modules\Demo\Models\Entities\Car';

    protected $table       = "car";
    protected $primaryKeys = array("carid");


    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        return $this->read("SELECT * FROM " . DB_PREFIX . "car");
    }

}
