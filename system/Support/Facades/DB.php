<?php 

namespace Support\Facades;

/**
 * @see \Database\DatabaseManager
 * @see \Database\Connection
 */
class DB extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'db'; }

}
