<?php
/**
 * Authorize - A Controller for managing the User Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Backend\Controllers;

use Reminders\ResetsPasswordsTrait;

use Backend\Controllers\BaseController;


class Reminders extends BaseController
{
    use ResetsPasswordsTrait;

    //
    protected $layout = 'Auth';

    protected $redirectTo = 'admin/dashboard';

}
