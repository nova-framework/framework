<?php

namespace App\Modules\VideoChat\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Assets;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;

use App\Core\BackendController;

use App\Modules\System\Exceptions\ValidationException;
use App\Modules\VideoChat\Helpers\VideoChat as ChatHelper;
use App\Modules\VideoChat\Models\ChatVideo;
use App\Modules\Users\Models\User;


class VideoChat extends BackendController
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
        $authUser = Auth::user();

        // Retrieve all other Users.
        $users = User::where('id', '!=', $authUser->id)->get();

        return $this->getView()
            ->shares('title', __d('video_chat', 'Video Chat'))
            ->withAuthUser($authUser)
            ->withUsers($users);
    }

    public function show($roomId)
    {
        $authUser = Auth::user();

        // Find the chat room instance.
        try {
            $chatVideo = ChatVideo::findOrFail($roomId);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('video_chat', 'The Chat Room with ID: {0} was not found.', $roomId);

            return Redirect::to('admin/dashboard')->withStatus($status, 'danger');
        }

        // Check if the current User is associated on this chat room.
        $isSender   = ($chatVideo->sender_id   === $authUser->id);
        $isReceiver = ($chatVideo->receiver_id === $authUser->id);

        // Calculate the other user ID, while checking if the current user belongs to this room.
        if ($isSender) {
            $chatUserId = $chatVideo->receiver_id;
        } elseif ($isReceiver) {
            $chatUserId = $chatVideo->sender_id;
        } else {
            $message = __d('video_chat', 'Trying to access a Chat Room where you do not belong.');

            return Redirect::to('admin/dashboard');
        }

        try {
            $chatUser = User::findOrFail($chatUserId);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('video_chat', 'The User with ID: {0} was not found.', $chatUserId);

            return Redirect::to('admin/dashboard')->withStatus($status, 'danger');
        }

        // Notify the other Chat User about the incoming Video Call.
        $chatLink = sprintf('<a href="%s" target="_blank"><b>%s</b></a>',
            site_url('admin/chat/video/' .$chatVideo->id),
            __d('video_chat', 'Video Chat')
        );

        $subject = __d('video_chat', 'Chat Request from {0}', $authUser->present()->name());
        $message = __d('video_chat', '<b>{0}</b> requested your presence on {1}.', $authUser->present()->name(), $chatLink);

        $chatUser->newNotification()
            ->from($authUser)
            ->withType('App.Modules.VideoChat.Request')
            ->withSubject($subject)
            ->withBody($message)
            ->regarding($chatVideo)
            ->deliver();

        // Calculate the Chat Room name, using a SHA256 string, resulting like:
        // f07b631f7a601cd8cbd3332d54f43142c7088a83299f859356f08d1d4d4259b3
        //
        $roomName = hash('sha256', site_url(sprintf('chat/video/%06d', $chatVideo->id)));

        // Additional assets required on the current View.
        $css = Assets::fetch('css', array(
            'https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/assets/css/emojione.min.css',
            resource_url('css/style.css', 'VideoChat'),
        ));

        $js = Assets::fetch('js', array(
            'https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/lib/js/emojione.min.js',
            resource_url('js/simplewebrtc-latest.js', 'VideoChat'),
        ));

        //
        $title = __d('video_chat', 'Video Chat : {0}', $authUser->present()->name());

        return $this->getView()
            ->shares('title', $title)
            ->shares('css', $css)
            ->shares('js', $js)
            ->with('roomName', $roomName)
            ->with('authUser', $authUser)
            ->with('chatUser', $chatUser);
    }

    public function create()
    {
        $authUser = Auth::user();

        //
        $input = Input::only('userId');

        // Create the Validator instance.
        $this->validate($input, array(
            'userId' => 'required|numeric|min:1',
        ));

        // First, retrieve the user using the user_id.
        $userId = $input['userId'];

        try {
            $chatUser = User::findOrFail($userId);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('video_chat', 'The User with ID: {0} was not found.', $userId);

            return Redirect::to('admin/dashboard')->withStatus($status, 'danger');
        }

        //
        $room = ChatHelper::getChatRoomByUsers($authUser->id, $chatUser->id);

        if ($room === false) {
            $createdRoom = ChatHelper::createRoom($authUser->id, $chatUser->id);

            if ($createdRoom === false) {
                $message = __d('video_chat', 'Chatroom could not be created');

                return Redirect::to('admin/dashboard')->withStatus($status, 'danger');
            }

            $room = ChatHelper::getChatRoomByUsers($authUser->id, $chatUser->id);
        }

        return Redirect::to('admin/chat/video/' .$room->id);
    }

}
