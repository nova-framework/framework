<?php

namespace AcmeCorp\Backend\Controllers;

use Nova\Foundation\Auth\AuthenticatesUsersTrait;
use Nova\Foundation\Auth\ThrottlesLoginsTrait;

use AcmeCorp\Backend\Controllers\BaseController;


class Authorize extends BaseController
{
    use AuthenticatesUsersTrait, ThrottlesLoginsTrait;

    //
    protected $layout = 'Auth';

    protected $redirectTo = 'admin/dashboard';
}
