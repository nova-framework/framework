<?php

namespace App\Modules\Messenger\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Session;
use Nova\Support\Facades\Validator;
use Nova\Support\Facades\View;

use App\Core\BackendController;

use App\Modules\Users\Models\User;
use App\Modules\Messenger\Models\Thread;
use App\Modules\Messenger\Models\Message;
use App\Modules\Messenger\Models\Participant;

use Carbon\Carbon;


class Messages extends BackendController
{

    protected function validate(array $data, $updating = false)
    {
        // The Validation Rules.
        if (! $updating) {
            $rules = array(
                'subject'    => 'required|min:3|valid_text',
                'message'    => 'required|min:3|valid_text',
                'recipients' => 'required|array'
            );
        } else {
            $rules = array(
                'message'    => 'required|min:3|valid_text',
                'recipients' => 'array'
            );
        }

        // The Validation Messages.
        $messages = array(
            'valid_text' => __d('messenger', 'The :attribute field is not a valid text.'),
        );

        // The Validation Attributes.
        $attributes = array(
            'subject'    => __d('messenger', 'Subject'),
            'message'    => __d('messenger', 'Message'),
            'recipients' => __d('messenger', 'Recipients'),
        );

        // The Extensions.
        Validator::extend('valid_text', function($attribute, $value, $parameters)
        {
            return ($value == strip_tags($value));
        });

        return Validator::make($data, $rules, $messages, $attributes);
    }

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
        //$threads = Thread::forUserWithNewMessages($userId)->latest('updated_at')->paginate(10);

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

            return Redirect::to('admin/messages')->withStatus($status, 'danger');
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
        // Validate the Input data.
        $input = Input::only('subject', 'message', 'recipients');

        $validator = $this->validate($input);

        if ($validator->passes()) {
            // Create the new Thread.
            $thread = Thread::create(array(
                'subject' => $input['subject'],
            ));

            // Create the new Message.
            Message::create(array(
                'thread_id' => $thread->id,
                'user_id'   => Auth::id(),
                'body'      => $input['message'],
            ));

            // Handle the Sender.
            Participant::create(array(
                'thread_id' => $thread->id,
                'user_id'   => Auth::id(),
                'last_read' => new Carbon()
            ));

            // Handle the Participants.
            if (Input::has('recipients')) {
                $thread->addParticipants($input['recipients']);
            }

            // Prepare the flash message.
            $status = __d('users', 'The Message <b>{0}</b> was successfully added.', $thread->subject);

            return Redirect::to('admin/messages')->withStatus($status);
        }

        // Errors occurred on Validation.
        $status = $validator->errors();

        return Redirect::back()->withInput()->withStatus($status, 'danger');
    }

    /**
     * Adds a new Message to a current Thread.
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

            return Redirect::to('admin/messages')->withStatus($status, 'danger');
        }

        // Validate the Input data.
        $input = Input::only('message', 'recipients');

        //
        $validator = $this->validate($input, true);

        if ($validator->passes()) {
            $thread->activateAllParticipants();

            // Create the new Message.
            Message::create(array(
                'thread_id' => $thread->id,
                'user_id'   => Auth::id(),
                'body'      => $input['message'],
            ));

            // Add the replier as a participant.
            $participant = Participant::firstOrCreate(array(
                'thread_id' => $thread->id,
                'user_id'   => Auth::id()
            ));

            $participant->last_read = new Carbon();

            $participant->save();

            // Handle the additional participants.
            if (Input::has('recipients')) {
                $thread->addParticipants($input['recipients']);
            }

            return Redirect::to('admin/messages/' .$id);
        }

        // Errors occurred on Validation.
        $status = $validator->errors();

        return Redirect::back()->withInput()->withStatus($status, 'danger');
    }

}
