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
     * "Users" table name to use for manual queries
     *
     * @var string|null
     */
    private $usersTable = null;

    /**
     * Messages relationship
     *
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany('App\Modules\Messenger\Models\Message');
    }

    /**
     * Returns the latest message from a thread
     *
     * @return \App\Modules\Messenger\Models\Message
     */
    public function getLatestMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Participants relationship
     *
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany('App\Modules\Messenger\Models\Participant');
    }

    /**
     * Returns the user object that created the thread
     *
     * @return mixed
     */
    public function creator()
    {
        $message = $this->messages()->oldest()->first();

        return $message->user;
    }

    /**
     * Returns all of the latest threads by updated_at date
     *
     * @return mixed
     */
    public static function getAllLatest()
    {
        return self::latest('updated_at');
    }

    /**
     * Returns an array of user ids that are associated with the thread
     *
     * @param null $userId
     * @return array
     */
    public function participantsUserIds($userId = null)
    {
        $users = $this->participants()->withTrashed()->lists('user_id');

        if ($userId) {
            $users[] = $userId;
        }

        return $users;
    }

    /**
     * Returns threads that the user is associated with
     *
     * @param $query
     * @param $userId
     *
     * @return mixed
     */
    public function scopeForUser($query, $userId)
    {
        return $query
            ->join('participants', 'threads.id', '=', 'participants.thread_id')
            ->where('participants.user_id', $userId)
            ->where('participants.deleted_at', null)
            ->select('threads.*');
    }

    /**
     * Returns threads with new messages that the user is associated with
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
            ->where(function ($query) {
                $connection = $this->getConnection();

                $tablePrefix = $connection->getTablePrefix();

                $query
                    ->where('threads.updated_at', '>', $connection->raw($tablePrefix .'participants.last_read'))
                    ->orWhereNull('participants.last_read');
            })
            ->select('threads.*');
    }

    /**
     * Returns threads between given user ids
     *
     * @param $query
     * @param $participants
     * @return mixed
     */
    public function scopeBetween($query, array $participants)
    {
        $query->whereHas('participants', function ($query) use ($participants) {
            $query->whereIn('user_id', $participants)
                ->groupBy('thread_id')
                ->havingRaw('COUNT(thread_id)='.count($participants));
        });
    }

    /**
     * Adds users to this thread
     *
     * @param array $participants list of all participants
     * @return void
     */
    public function addParticipants(array $participants)
    {
        if (count($participants)) {
            foreach ($participants as $user_id) {
                Participant::firstOrCreate(array(
                    'user_id' => $user_id,
                    'thread_id' => $this->id,
                ));
            }
        }
    }

    /**
     * Mark a thread as read for a user
     *
     * @param integer $userId
     */
    public function markAsRead($userId)
    {
        try {
            $participant = $this->getParticipantFromUser($userId);

            $participant->last_read = new Carbon();

            $participant->save();
        } catch (ModelNotFoundException $e) {
            // Do nothing.
        }
    }

    /**
     * See if the current thread is unread by the user
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
        } catch (ModelNotFoundException $e) {
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
     * Restores all participants within a thread that has a new message
     */
    public function activateAllParticipants()
    {
        $participants = $this->participants()->withTrashed()->get();

        foreach ($participants as $participant) {
            $participant->restore();
        }
    }

    /**
     * Generates a string of participant information
     *
     * @param null $userId
     * @param array $columns
     * @return string
     */
    public function participantsString($userId = null, $columns = array('username'))
    {
        $participantsTable = 'participants';

        $usersTable = $this->getUsersTable();

        $selectString = $this->createSelectString($columns);

        $participantNames = $this->getConnection()->table($usersTable)
            ->join($participantsTable, $usersTable . '.id', '=', $participantsTable . '.user_id')
            ->where($participantsTable . '.thread_id', $this->id)
            ->select($this->getConnection()->raw($selectString));

        if ($userId !== null) {
            $participantNames->where($usersTable . '.id', '!=', $userId);
        }

        return $participantNames->implode('name', ', ');
    }

    /**
     * Checks to see if a user is a current participant of the thread
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
     * Generates a select string used in participantsString()
     *
     * @param $columns
     * @return string
     */
    protected function createSelectString($columns)
    {
        $dbDriver    = $this->getConnection()->getDriverName();
        $tablePrefix = $this->getConnection()->getTablePrefix();

        $usersTable = $this->getUsersTable();

        switch ($dbDriver) {
        case 'pgsql':
        case 'sqlite':
            $columnString = implode(" || ' ' || " . $tablePrefix . $usersTable . '.', $columns);
            $selectString = '(' . $tablePrefix . $usersTable . '.' . $columnString . ') as name';

            break;
        case 'sqlsrv':
            $columnString = implode(" + ' ' + " . $tablePrefix . $usersTable . '.', $columns);
            $selectString = '(' . $tablePrefix . $usersTable . '.' . $columnString . ') as name';

            break;
        default:
            $columnString = implode(", ' ', " . $tablePrefix . $usersTable . '.', $columns);
            $selectString = 'concat(' . $tablePrefix . $usersTable . '.' . $columnString . ') as name';
        }

        return $selectString;
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
     * Returns the "users" table name to use in manual queries
     *
     * @return string
     */
    private function getUsersTable()
    {
        if ($this->usersTable !== null) {
            return $this->usersTable;
        }

        return $this->usersTable = with(new User())->getTable();
    }
}
