<?php

namespace Shared\Auth\Reminders;


interface RemindableInterface
{
    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail();

    /**
     * Send the password reset notification.
     *
     * @param  string  $hash
     * @param  int     $timestamp
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($hash, $timestamp, $token);
}
