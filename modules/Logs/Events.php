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
use Modules\Logs\Models\Log as Logger;
use Modules\Logs\Models\LogGroup;


Event::listen('auth.login', function($user, $remember)
{
    $group = LogGroup::where('slug', 'auth')
        ->remember(1440)
        ->first();

    // Create the Log entry.
    Logger::create(array(
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
    Logger::create(array(
        'user_id'  => $user->getKey(),
        'group_id' => $group->getKey(),
        'message'  => __d('logs', 'The User logged out.'),
        'url'      => Request::header('referer'),
    ));
});

// Logs the Site Settings updates.
Event::listen('app.modules.system.settings.updated', function($user, $options)
{
    $group = LogGroup::where('slug', 'system')
        ->remember(1440)
        ->first();

    // Create the Log entry.
    Logger::create(array(
        'user_id'  => $user->getKey(),
        'group_id' => $group->getKey(),
        'message'  => __d('logs', 'The Site Settings was updated.'),
        'url'      => Request::header('referer'),
        // We store also the current options on Event Data.
        'data'     => $options,
    ));
});
