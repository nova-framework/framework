<?php

namespace Auth\Reminders;


interface ReminderRepositoryInterface
{
    /**
     * Create a new reminder record and token.
     *
     * @param  \Auth\Reminders\RemindableInterface  $user
     * @return string
     */
    public function create(RemindableInterface $user);

    /**
     * Determine if a reminder record exists and is valid.
     *
     * @param  \Auth\Reminders\RemindableInterface  $user
     * @param  string  $token
     * @return bool
     */
    public function exists(RemindableInterface $user, $token);

    /**
     * Delete a reminder record by token.
     *
     * @param  string  $token
     * @return void
     */
    public function delete($token);

    /**
     * Delete expired reminders.
     *
     * @return void
     */
    public function deleteExpired();

}
