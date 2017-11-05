<?php
/**
 * Bootstrap - the Module's specific Bootstrap.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


/**
 * Register the Widgets.
 */
Widget::register('App\Modules\Users\Widgets\RegisteredUsers', 'registeredUsers', 'backend.dashboard.top', 3);
