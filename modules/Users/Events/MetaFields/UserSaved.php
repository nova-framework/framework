<?php

namespace Modules\Users\Events\MetaFields;

use Nova\Foundation\Events\DispatchableTrait;

use Modules\Users\Models\User;


class UserSaved
{
    use DispatchableTrait;

    /**
     * @var \Modules\Users\Models\User
     */
    public $user;


    /**
     * Create a new Event instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

}
