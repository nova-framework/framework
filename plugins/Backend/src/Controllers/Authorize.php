<?php

namespace Backend\Controllers;

use Nova\Foundation\Auth\AuthenticatesUsersTrait;
use Nova\Foundation\Auth\ThrottlesLoginsTrait;

use Backend\Controllers\BaseController;


class Authorize extends BaseController
{
	use AuthenticatesUsersTrait, ThrottlesLoginsTrait;

	//
	protected $layout = 'Auth';

	protected $redirectTo = 'admin/dashboard';
}
