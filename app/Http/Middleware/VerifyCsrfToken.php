<?php

namespace App\Http\Middleware;

use Nova\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;


class VerifyCsrfToken extends BaseVerifier
{
	/**
	 * The URIs that should be excluded from CSRF verification.
	 *
	 * @var array
	 */
	protected $except = array(
		'auth/logout',
		'admin/files/connector'
	);
}
