<?php

namespace Modules\Users\Policies;

use Modules\Users\Models\User;
use Modules\Profile\Models\Profile;

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
