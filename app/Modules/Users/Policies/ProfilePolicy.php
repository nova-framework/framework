<?php

namespace App\Modules\Users\Policies;

use App\Modules\Users\Models\User;
use App\Modules\Profile\Models\Profile;

use Nova\Auth\Access\HandlesAuthorizationTrait;


class ProfilePolicy
{
    use HandlesAuthorizationTrait;


    /**
     * Determine whether the user can manage the permissions.
     *
     * @param  \App\Models\User  $authUser
     * @return mixed
     */
    public function manage(User $authUser)
    {
        return $authUser->hasPermission('platform.profiles.manage');
    }
}
