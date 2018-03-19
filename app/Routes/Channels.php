<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use Modules\Users\Models\User;


Broadcast::channel('Modules.Users.Models.User.{id}', function (User $user, $id)
{
    return (int) $user->id === (int) $id;
});
