<?php
/**
 * Abstract ORM Entity.
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 19th, 2015
 */

namespace Nova\ORM;

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

    public function __construct()
    {
        // Reset the state
        $this->_state = 0;

        // Let the entity be indexed, the annotations will be read into the Structure cache.
        Structure::indexEntity($this);
    }
}
