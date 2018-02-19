<?php

namespace Modules\Users\Events\MetaFields;

use Nova\Foundation\Events\DispatchableTrait;
use Nova\Validation\Validator;

use App\Events\Event;

use Modules\Users\Models\User;


class UserValidation extends Event
{
    use DispatchableTrait;

    /**
     * @var \Nova\Validation\Validator
     */
    public $validator;

    /**
     * @var \Modules\Users\Models\User
     */
    public $user;


    /**
     * Create a new Event instance.
     *
     * @return void
     */
    public function __construct(Validator $validator, User $user = null)
    {
        $this->validator = $validator;
        $this->user      = $user;
    }

}
