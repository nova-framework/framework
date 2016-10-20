<?php
/**
 * View - load template pages
 *
 * @author David Carr - dave@novaframework.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Nova\Support\Facades;

use Nova\Support\Facades\Facade;


/**
 * @see \Nova\View\Factory
 * @see \Nova\View\View
 */
class View extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'view'; }

}
