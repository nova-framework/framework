<?php

namespace Modules\Users\Listeners;

use Modules\Platform\Listeners\MetaFields\BaseListener;
use Modules\Users\Events\MetaFields\UserValidation;
use Modules\Users\Events\MetaFields\UserEditing;
use Modules\Users\Events\MetaFields\UserSaving;

use BadMethodCallException;


class MetaFields extends BaseListener
{

    /**
     * Handle the event.
     *
     * @param  Modules\Users\Events\UserEditing  $event
     * @return void
     */
    public function edit(UserEditing $event)
    {
        if (! is_null($user = $event->user)) {
            $meta = $user->meta;
        } else {
            $meta = null;
        }

        return $this->createView()
            ->with('request', $this->getRequest())
            ->with('meta', $meta)
            ->render();
    }

    /**
     * Handle the event.
     *
     * @param  Modules\Users\Events\UserValidation  $event
     * @return void
     */
    public function validate(UserValidation $event)
    {

    }

    /**
     * Handle the event.
     *
     * @param  Modules\Users\Events\UserEditing  $event
     * @return void
     */
    public function save(UserSaving $event)
    {
    }
}
