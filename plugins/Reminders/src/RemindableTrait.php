<?php

namespace AcmeCorp\Reminders;

use AcmeCorp\Reminders\Notifications\ResetPassword as ResetPasswordNotification;


trait RemindableTrait
{
    /**
     * Get the e-mail address where password Reminders are sent.
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
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
