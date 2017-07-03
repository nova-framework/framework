<?php

/*
|--------------------------------------------------------------------------
| Plugin Configuration
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Configuration for the plugin.
*/

return array(

	/*
	|--------------------------------------------------------------------------
	| Default Password Broker
	|--------------------------------------------------------------------------
	|
	| This option controls the default password broker. You may change this
	| default as required, but it's a perfect start for most applications.
	|
	*/

	'default' => 'users',

	/*
	|--------------------------------------------------------------------------
	| Resetting Passwords
	|--------------------------------------------------------------------------
	|
	| Here you may set the options for resetting passwords including the view
	| that is your password reset e-mail. You may also set the name of the
	| table that maintains all of the reset tokens for your application.
	|
	| You may specify multiple password reset configurations if you have more
	| than one user table or model in the application and you want to have
	| separate password reset settings based on the specific user types.
	|
	| The expire time is the number of minutes that the reset token should be
	| considered valid. This security feature keeps tokens short-lived so
	| they have less time to be guessed. You may change this as needed.
	|
	*/

	'reminders' => array(
		'users' => array(
			'provider'	=> 'users',
			'email'		=> 'Emails/Auth/Reminder',
			'table'		=> 'password_reminders',
			'expire'	=> 60,
		),
	),
);
