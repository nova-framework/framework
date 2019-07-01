<?php

namespace Modules\Platform\Policies;

use Modules\Users\Models\User;

use Nova\Auth\Access\HandlesAuthorizationTrait;


class OptionPolicy
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
        return $authUser->hasPermission('platform.settings.manage');
    }
}
