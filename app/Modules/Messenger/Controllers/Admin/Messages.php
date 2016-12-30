<?php

namespace App\Modules\Messenger\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Session;
use Nova\Support\Facades\View;

use App\Core\BackendController;
use App\Models\User;

use App\Modules\Messenger\Models\Thread;
use App\Modules\Messenger\Models\Message;
use App\Modules\Messenger\Models\Participant;

use Carbon\Carbon;


class Messages extends BackendController
{

    /**
     * Show all of the message threads to the user
     *
     * @return mixed
     */
    public function index()
    {
        $userId = Auth::id();

        // All Threads, ignore deleted/archived participants.
        $threads = Thread::latest('updated_at')->paginate(10);

        // All Threads that User is participating in.
        //$threads = Thread::forUser($userId)->latest('updated_at')->paginate(10);

        // All Threads that User is participating in, with new messages.
        // $threads = Thread::forUserWithNewMessages($userId)->latest('updated_at')->paginate(10);

        return $this->getView()
            ->shares('title', __d('messenger', 'Messages'))
            ->withThreads($threads)
            ->withUserId($userId);
    }

    /**
     * Shows a message thread
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $userId = Auth::id();

        try {
            $thread = Thread::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('messenger', 'The thread with ID: {0} was not found.', $id);

            return Redirect::to('admin/messages')->withStatus($status);
        }

        // Show current User in list if not a current participant.
        // $users = User::whereNotIn('id', $thread->participantsUserIds())->get();

        // Don't show the current user in list
        $users = User::whereNotIn('id', $thread->participantsUserIds($userId))->get();

        $thread->markAsRead($userId);

        return $this->getView()
            ->shares('title', __d('messenger', 'Show Thread'))
            ->withThread($thread)
            ->withUsers($users);
    }

    /**
     * Creates a new message thread
     *
     * @return mixed
     */
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();

        return $this->getView()
            ->shares('title', __d('messenger', 'Create Thread'))
            ->withUsers($users);
    }

    /**
     * Stores a new Message Thread
     *
     * @return mixed
     */
    public function store()
    {
        $input = Input::all();

        $thread = Thread::create(array(
            'subject' => $input['subject'],
        ));

        // Message

        Message::create(array(
            'thread_id' => $thread->id,
            'user_id'   => Auth::id(),
            'body'      => $input['message'],
        ));

        // Sender

        Participant::create(array(
            'thread_id' => $thread->id,
            'user_id'   => Auth::id(),
            'last_read' => new Carbon
        ));

        // Recipients

        if (Input::has('recipients')) {
            $thread->addParticipants($input['recipients']);
        }

        return Redirect::to('admin/messages');
    }

    /**
     * Adds a new message to a current thread
     *
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        try {
            $thread = Thread::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('messenger', 'The thread with ID: {0} was not found.', $id);

            return Redirect::to('admin/messages')->withStatus($status);
        }

        $thread->activateAllParticipants();

        // Message

        Message::create(array(
            'thread_id' => $thread->id,
            'user_id'   => Auth::id(),
            'body'      => Input::get('message'),
        ));

        // Add replier as a participant

        $participant = Participant::firstOrCreate(array(
            'thread_id' => $thread->id,
            'user_id'   => Auth::id()
        ));

        $participant->last_read = new Carbon();

        $participant->save();

        // Recipients
        if (Input::has('recipients')) {
            $thread->addParticipants(Input::get('recipients'));
        }

        return Redirect::to('admin/messages/' .$id);
    }

}
