<?php

namespace App\Modules\Messenger\Models;

use Nova\Database\ORM\Model;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Database\ORM\SoftDeletingTrait;
use Nova\Support\Facades\Config;

use App\Models\User;

use Carbon\Carbon;


class Thread extends Model
{
    use SoftDeletingTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'threads';

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = array('subject');

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = array('created_at', 'updated_at', 'deleted_at');

    /**
     * "Users" table name to use for manual queries.
     *
     * @var string|null
     */
    private $usersTable = null;

    /**
     * Messages relationship.
     *
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany('App\Modules\Messenger\Models\Message');
    }

    /**
     * Returns the latest message from a thread.
     *
     * @return \App\Modules\Messenger\Models\Message
     */
    public function getLatestMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Participants relationship.
     *
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany('App\Modules\Messenger\Models\Participant');
    }

    /**
     * Returns the user object that created the thread.
     *
     * @return mixed
     */
    public function creator()
    {
        $message = $this->messages()->oldest()->first();

        return $message->user;
    }

    /**
     * Returns an array of User IDs that are associated with the thread.
     *
     * @param null $userId
     * @return array
     */
    public function participantsUserIds($userId = null)
    {
        $users = $this->participants()->withTrashed()->lists('user_id');

        if (! is_null($userId)) {
            array_push($users, $userId);
        }

        return $users;
    }

    /**
     * Returns threads that the User is associated with.
     *
     * @param $query
     * @param $userId
     *
     * @return mixed
     */
    public function scopeForUser($query, $userId)
    {
        return $query->join('participants', 'threads.id', '=', 'participants.thread_id')
            ->where('participants.user_id', $userId)
            ->where('participants.deleted_at', null)
            ->select('threads.*');
    }

    /**
     * Returns threads with new messages that the User is associated with.
     *
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeForUserWithNewMessages($query, $userId)
    {
        return $query->join('participants', 'threads.id', '=', 'participants.thread_id')
            ->where('participants.user_id', $userId)
            ->whereNull('participants.deleted_at')
            ->where(function ($query)
            {
                $connection = $this->getConnection();

                $tablePrefix = $connection->getTablePrefix();

                return $query->where('threads.updated_at', '>', $connection->raw($tablePrefix .'participants.last_read'))
                    ->orWhereNull('participants.last_read');
            })
            ->select('threads.*');
    }

    /**
     * Returns threads between given User IDs.
     *
     * @param $query
     * @param $participants
     * @return mixed
     */
    public function scopeBetween($query, array $participants)
    {
        return $query->whereHas('participants', function ($query) use ($participants)
        {
            return $query->whereIn('user_id', $participants)
                ->groupBy('thread_id')
                ->havingRaw('COUNT(thread_id) = ' .count($participants));
        });
    }

    /**
     * Adds users to this Thread.
     *
     * @param array $participants list of all participants
     * @return void
     */
    public function addParticipants(array $participants)
    {
        foreach ($participants as $user_id) {
            Participant::firstOrCreate(array(
                'user_id'   => $user_id,
                'thread_id' => $this->id,
            ));
        }
    }

    /**
     * Mark a thread as read for a User.
     *
     * @param integer $userId
     */
    public function markAsRead($userId)
    {
        try {
            $participant = $this->getParticipantFromUser($userId);

            $participant->last_read = new Carbon();

            $participant->save();
        }
        catch (ModelNotFoundException $e) {
            // Do nothing.
        }
    }

    /**
     * See if the current Thread is unread by the User.
     *
     * @param integer $userId
     * @return bool
     */
    public function isUnread($userId)
    {
        try {
            $participant = $this->getParticipantFromUser($userId);

            if ($this->updated_at > $participant->last_read) {
                return true;
            }
        }
        catch (ModelNotFoundException $e) {
            // Do nothing.
        }

        return false;
    }

    /**
     * Finds the participant record from a user id
     *
     * @param $userId
     * @return mixed
     * @throws \Nova\Database\ORM\ModelNotFoundException
     */
    public function getParticipantFromUser($userId)
    {
        return $this->participants()->where('user_id', $userId)->firstOrFail();
    }

    /**
     * Restores all participants within a Thread that has a new message
     */
    public function activateAllParticipants()
    {
        $participants = $this->participants()->withTrashed()->get();

        foreach ($participants as $participant) {
            $participant->restore();
        }
    }

    /**
     * Generates a string of participant information.
     *
     * @param null $userId
     * @param string $column
     * @return string
     */
    public function participantsString($userId = null, $column = 'username')
    {
        $connection = $this->getConnection();
        $usersTable = $this->getUsersTable();

        $participants = $connection->table($usersTable)
            ->join('participants', $usersTable . '.id', '=', 'participants.user_id')
            ->where('participants.thread_id', $this->id)
            ->select($usersTable .'.' .$column);

        if ($userId !== null) {
            $participants->where($usersTable . '.id', '!=', $userId);
        }

        return $participants->implode($column, ', ');
    }

    /**
     * Checks to see if a User is a current participant of the Thread.
     *
     * @param $userId
     * @return bool
     */
    public function hasParticipant($userId)
    {
        $participants = $this->participants()->where('user_id', '=', $userId);

        if ($participants->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Sets the "users" table name
     *
     * @param $tableName
     */
    public function setUsersTable($tableName)
    {
        $this->usersTable = $tableName;
    }

    /**
     * Returns the "users" table name to use in manual queries.
     *
     * @return string
     */
    private function getUsersTable()
    {
        if ($this->usersTable !== null) {
            return $this->usersTable;
        }

        // Create a new User instance.
        $user = new User();

        return $this->usersTable = $user->getTable();
    }
}
