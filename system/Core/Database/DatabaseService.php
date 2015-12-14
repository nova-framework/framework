<?php


namespace Smvc\Core\Database;
use Smvc\Core\Database\Engine\Engine;
use Smvc\Core\Database\Engine\MySQLEngine;

/**
 * Class DatabaseService.
 * @package Core\Database
 */
abstract class DatabaseService
{
    /** @var string Driver name, should be in the config as default. */
    protected $driver;

    /** @var Engine|MySQLEngine database engine we will use. */
    protected $engine;

    /** @var string Table name. */
    protected $table;

    /** @var string[]|array Primary keys. */
    protected $primaryKeys;


    /**
     * DatabaseService constructor.
     * @param Engine|null $engine
     */
    public function __construct($engine = null)
    {
        if ($engine === null || !$engine instanceof Engine)
        {
            $engine = EngineFactory::getEngine();
        }

        $this->engine = $engine;
    }
}
