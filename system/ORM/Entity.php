<?php
/**
 * Abstract ORM Entity.
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 19th, 2015
 */

namespace Nova\ORM;

use Doctrine\DBAL\Query\QueryBuilder;
use Nova\DBAL\Connection;
use Nova\DBAL\Manager as DBALManager;

/**
 * Class Entity, can be extended with your database entities
 */
abstract class Entity
{
    /**
     * Hold the state of the current Entity. Will be used to determinate if INSERT or UPDATE is needed
     *
     *  0 - Unsaved
     *  1 - Fetched, already in database
     *
     * @var int
     */
    private $_state = 0;

    /**
     * Link name for using in this entity
     *
     * @var string
     */
    private static $_linkName = 'default';

    /**
     * Link instance (DBAL instance) used for this entity.
     *
     * @var null|Connection
     */
    private static $_link = null;

    /**
     * Indexed table annotation data
     *
     * @var Annotation\Table|null
     */
    private static $_table = null;

    /**
     * Will be called each time a static call is made, to check if the entity is indexed
     *
     * @param $method
     * @param $parameters
     */
    public static function __callStatic($method, $parameters){
        echo __CLASS__ . "::" . $method;
        if (method_exists(__CLASS__, $method)) {
            self::discoverEntity();
            forward_static_call_array(array(__CLASS__,$method),$parameters);
        }
    }

    /**
     * Index entity if needed.
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    private static function discoverEntity()
    {
        if (!self::$_table) {
            // Index entity.
            self::$_table = Structure::indexEntity(self::class);
            self::$_linkName = self::$_table->link;
        }
        self::$_link = null;
        self::$_linkName = "";
    }

    /**
     * Get Link instance
     *
     * @return Connection
     */
    private static function getLink()
    {
        if (self::$_link == null) {
            self::$_link = DBALManager::getConnection(self::$_linkName);

            $class = new \ReflectionClass(self::class);
            self::$_link->setFetchType($class->getName());
        }
        return self::$_link;
    }


    /**
     * Query Builder for finding
     *
     * @return QueryBuilder
     */
    public static function find()
    {
        self::getLink()->createQueryBuilder();
    }


    /**
     * Get from database with primary key value.
     * @param string|int $id Primary key value
     *
     * @return Entity
     */
    public static function get($id)
    {
        
    }
}
