<?php

namespace Shared\Auth\Reminders;

use Shared\Auth\Notifications\ResetPassword as ResetPasswordNotification;


trait RemindableTrait
{
    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($hash, $token)
    {
        $this->notify(new ResetPasswordNotification($hash, $token));
    }
}
