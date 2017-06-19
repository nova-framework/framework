<?php

/*
|--------------------------------------------------------------------------
| Plugin Bootstrap
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Bootstrap for the Plugin.
*/


/**
 * Listener Closure to the Event 'backend.menu.sidebar'.
 */
Event::listen('backend.menu.sidebar', function($menu, $user)
{
	if (! $user->hasRole('administrator')) {
		return;
	}

	$menu->addItem('fileManager', __d('file_manager', 'Files'), site_url('admin/files'), 4, 'file');
});
