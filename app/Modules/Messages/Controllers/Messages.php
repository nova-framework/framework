<?php

namespace App\Modules\Messages\Controllers;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;
use Nova\Validation\ValidationException;

use App\Modules\Platform\Controllers\BaseController;

use App\Modules\Messages\Models\Message;
use App\Modules\Users\Models\User;


class Messages extends BaseController
{

    protected function validateInput(array $data, array $rules, array $messages = array(), array $attributes = array())
    {
        $validator = Validator::make($data, $rules, $messages, $attributes);

        // Go Exception if the data validation fails.
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function index()
    {
        $authUser = Auth::user();

        // Load the messages of the current logged in user and pass them to the view.
        $messages = Message::with('sender', 'receiver', 'replies')
            ->notReply()
            ->where(function($query) use ($authUser)
            {
                $query->where('sender_id', $authUser->id)->orWhere('receiver_id', $authUser->id);

            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return $this->createView()
            ->shares('title', __d('messages', 'Messages'))
            ->with(compact('authUser', 'messages'));
    }

    public function create()
    {
        $authUser = Auth::user();

        // Retrieve all other Users.
        $users = User::where('id', '!=', $authUser->id)->get();

        return $this->createView()
            ->shares('title', __d('messages', 'Send Message'))
            ->with(compact('authUser', 'users'));
    }

    /**
     * Post a status.
     */
    public function store()
    {
        $authUser = Auth::user();

        //
        $input = Input::only('subject', 'message', 'user');

        // Create the Validator instance.
        $this->validateInput($input, array(
            'subject' => 'required|min:3|max:100',
            'message' => 'required|min:3|max:1000',
            'user'    => 'required|numeric|min:1',
        ));

        // First, retrieve the user using the receiverId.
        $userId = $input['user'];

        if ($userId == $authUser->id) {
            // No talking with himself allowed.
            return Redirect::to('admin/dashboard');
        }

        try {
             $user = User::findOrFail($userId);
        }
        catch (ModelNotFoundException $e) {
            $message = __d('messages', 'The User with ID: {0} was not found.', $userId);

            return Redirect::to('admin/dashboard')->with('warning', $status);
        }

        $message = Message::create(array(
            'subject' => $input['subject'],
            'body'    => $input['message'],
        ));

        $message->sender()->associate($authUser);

        $message->receiver()->associate($user);

        $authUser->messages()->save($message);

        // Prepare the flash message.
        $status = __d('messages', 'Message Posted.');

        return Redirect::to('messages')->with('success', $status);
    }

    public function show($threadId)
    {
        $authUser = Auth::user();

        // Find the status that we need to reply to.
        try {
             $message = Message::with('sender', 'receiver', 'replies')->notReply()->findOrFail($threadId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/dashboard');
        }

        // Mark the message and its replies as seen.
        $message->setReadBy($authUser);

        foreach ($message->replies as $reply) {
            $reply->setReadBy($authUser);
        }

        // Recalculate the number of unread messages.
        $messageCount = Message::where('receiver_id', $authUser->id)->unread()->count();

        return $this->createView()
            ->shares('title', __d('messages', 'Show Message'))
            ->shares('privateMessageCount', $messageCount)
            ->with(compact('authUser', 'message'));
    }

    /**
     * Reply to a status.
     */
    public function reply($threadId)
    {
        $input = Input::only('reply');

        // Create the Validator instance.
        $this->validateInput($input, array(
            'reply' => 'required|min:3|max:1000',
        ), array(
            'required' => __d('messages', 'You must type a reply first!')
        ));

        // Find the status that we need to reply to.
        try {
             $thread = Message::notReply()->findOrFail($threadId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/dashboard');
        }

        $authUser = Auth::user();

        if ($authUser->id !== $thread->sender->id) {
            $user = $thread->sender;
        } else {
            $user = $thread->receiver;
        }

        // associate() works from the belongsTo side of the relationship
        // a particular reply is associated with the user who made that
        $reply = Message::create(array(
            'body' => $input['reply'],
        ));

        $reply->sender()->associate($authUser);

        $reply->receiver()->associate($user);

        $thread->replies()->save($reply);

        $thread->touch();

        return Redirect::back();
    }

}
