<?php

namespace Modules\Users\Events\MetaFields;

use Nova\Foundation\Events\DispatchableTrait;

use App\Events\Event;

use Modules\Users\Models\User;


class UserDeleting extends Event
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
    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

}
