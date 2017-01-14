<?php

namespace App\Modules\Messages\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;

use App\Core\BackendController;

use App\Modules\System\Exceptions\ValidationException;
use App\Modules\Messages\Models\Message;
use App\Modules\Users\Models\User;


class Messages extends BackendController
{

    protected function validate(array $data, array $rules, array $messages = array(), array $attributes = array())
    {
        $validator = Validator::make($data, $rules, $messages, $attributes);

        // Go Exception if the data validation fails.
        if ($validator->fails()) {
            throw new ValidationException('Validation failed', $validator->errors());
        }
    }

    public function index()
    {
        $user = Auth::user();

        // Load the messages of the current logged in user and pass them to the view.
        $messages = Message::with('replies')
            ->notReply()
            ->where(function($query) use ($user)
            {
                $query->where('sender_id', $user->id)->orWhere('receiver_id', $user->id);

            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        //
        $title = __d('messages', 'Messages | {0}', $user->present()->name());

        return $this->getView()
            ->shares('title', $title)
            ->withAuthUser($user)
            ->withMessages($messages);
    }

    public function create()
    {
        $authUser = Auth::user();

        // Retrieve all other Users.
        $users = User::where('id', '!=', $authUser->id)->get();

        //
        $title = __d('messages', 'Create Message | {0}', $authUser->present()->name());

        return $this->getView()
            ->shares('title', $title)
            ->withAuthUser($authUser)
            ->withUsers($users);
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
        $this->validate($input, array(
            'subject' => 'required|min:3|max:100',
            'message' => 'required|min:3|max:1000',
            'user'    => 'required|numeric|min:1',
        ));

        // First, retrieve the user using the receiverId.
        $userId = $input['user'];

        try {
             $user = User::findOrFail($userId);
        }
        catch (ModelNotFoundException $e) {
            $message = __d('messages', 'The User with ID: {0} was not found.', $userId);

            return Redirect::to('admin/dashboard')->withStatus($status, 'danger');
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

        return Redirect::to('admin/messages')->withStatus($status);
    }

    public function show($threadId)
    {
        $user = Auth::user();

        // Find the status that we need to reply to.
        try {
             $message = Message::notReply()->findOrFail($threadId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/dashboard');
        }

        // Mark the message and its replies as seen.
        $message->setReadBy($user);

        foreach ($message->replies as $reply) {
            $reply->setReadBy($user);
        }

        // Recalculate the number of unread messages.
        $messageCount = Message::where('receiver_id', $user->id)->unread()->count();

        //
        $title = __d('messages', 'Show Message | {0}', $user->present()->name());

        return $this->getView()
            ->shares('title', $title)
            ->shares('messageCount', $messageCount)
            ->withAuthUser($user)
            ->withMessage($message);
    }

    /**
     * Reply to a status.
     */
    public function reply($threadId)
    {
        $input = Input::only('reply');

        // Create the Validator instance.
        $this->validate($input, array(
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
