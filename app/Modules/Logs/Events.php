<?php
/**
 * Events - all Module's specific Events are defined here.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


/** Define Events. */

Event::listen('backend.menu', function($user)
{
    if (! $user->hasRole('administrator')) {
        return array();
    }

    $items = array(
        array(
            'uri'    => 'admin/logs',
            'title'  => __d('logs', 'Logs'),
            'label'  => '',
            'icon'   => 'server',
            'weight' => 7,
        ),
    );

    return $items;
});

// Log the User's login and logout.
use App\Modules\Logs\Models\Log as AuthLog;
use App\Modules\Logs\Models\LogGroup;

Event::listen('auth.login', function($user, $remember)
{
    $group = LogGroup::where('slug', 'auth')
        ->remember(1440)
        ->first();

    // Create the Log entry.
    AuthLog::create(array(
        'user_id'  => $user->getKey(),
        'group_id' => $group->getKey(),
        'message'  => __d('logs', 'The User logged in.'),
        'url'      => Request::header('referer'),
    ));
});

Event::listen('auth.logout', function($user)
{
    $group = LogGroup::where('slug', 'auth')
        ->remember(1440)
        ->first();

    // Create the Log entry.
    AuthLog::create(array(
        'user_id'  => $user->getKey(),
        'group_id' => $group->getKey(),
        'message'  => __d('logs', 'The User logged out.'),
        'url'      => Request::header('referer'),
    ));
});
