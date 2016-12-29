<?php

namespace App\Modules\Messenger\Traits;

use App\Modules\Messenger\Models\Thread;
use App\Modules\Messenger\Models\Participant;


trait Messagable
{
    /**
     * Message relationship
     *
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany('App\Modules\Messenger\Models\Message');
    }

    /**
     * Thread relationship
     *
     * @return \Nova\Database\ORM\Relations\belongsToMany
     */
    public function threads()
    {
        return $this->belongsToMany('App\Modules\Messenger\Models\Thread', 'participants');
    }

    /**
     * Returns the new Messages count for User
     *
     * @return int
     */
    public function newMessagesCount()
    {
        $threads = $this->threadsWithNewMessages();

        return count($threads);
    }

    /**
     * Returns all Threads with new Messages
     *
     * @return array
     */
    public function threadsWithNewMessages()
    {
        $threadsWithNewMessages = array();

        $participants = Participant::where('user_id', $this->id)->lists('last_read', 'thread_id');

        if ($participants) {
            $threads = Thread::whereIn('id', array_keys($participants))->get();

            foreach ($threads as $thread) {
                if ($thread->updated_at > $participants[$thread->id]) {
                    $threadsWithNewMessages[] = $thread->id;
                }
            }
        }

        return $threadsWithNewMessages;
    }
}
