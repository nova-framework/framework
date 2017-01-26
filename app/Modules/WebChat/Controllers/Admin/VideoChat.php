<?php

namespace App\Modules\VideoChat\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;

use App\Modules\System\Exceptions\ValidationException;
use App\Modules\VideoChat\Helpers\VideoChat as ChatHelper;
use App\Modules\VideoChat\Models\ChatVideo;
use App\Modules\Users\Models\User;
use App\Modules\WebChat\Core\Controller;


class VideoChat extends Controller
{

    public function index()
    {
        $authUser = Auth::user();

        // Retrieve all other Users.
        $users = User::where('id', '!=', $authUser->id)->get();

        return $this->getView()
            ->shares('title', __d('web_chat', 'Video Chat'))
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
            $status = __d('web_chat', 'The Chat Room with ID: {0} was not found.', $roomId);

            return Redirect::to('admin/dashboard')->withStatus($status, 'danger');
        }

        // Check if the current User is associated on this chat room and calculate the other User ID.
        if ($chatVideo->sender_id === $authUser->id) {
            $chatUserId = $chatVideo->receiver_id;
        } else if ($chatVideo->receiver_id === $authUser->id) {
            $chatUserId = $chatVideo->sender_id;
        } else {
            // The current User does not belong to this chat room.
            $status = __d('web_chat', 'Trying to access a Chat Room where you do not belong.');

            return Redirect::to('admin/dashboard')->withStatus($status, 'danger');
        }

        try {
            $chatUser = User::findOrFail($chatUserId);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('web_chat', 'The User with ID: {0} was not found.', $chatUserId);

            return Redirect::to('admin/dashboard')->withStatus($status, 'danger');
        }

        // Notify the other Chat User about the incoming Video Call.
        $chatLink = sprintf('<a href="%s" target="_blank"><b>%s</b></a>',
            site_url('admin/chat/video/' .$chatVideo->id),
            __d('web_chat', 'Video Chat')
        );

        $subject = __d('web_chat', 'Chat Request from {0}', $authUser->present()->name());
        $message = __d('web_chat', '<b>{0}</b> requested your presence on {1}.', $authUser->present()->name(), $chatLink);

        $chatUser->newNotification()
            ->from($authUser)
            ->withType('App.Modules.VideoChat.Request')
            ->withSubject($subject)
            ->withBody($message)
            ->regarding($chatVideo)
            ->deliver();

        // Retrieve the Signaling Server from configuration.
        $url = Config::get('videoChat.url', 'https://sandbox.simplewebrtc.com:443/');

        // Calculate the Chat Room name, using a SHA256 string, resulting something like:
        // f07b631f7a601cd8cbd3332d54f43142c7088a83299f859356f08d1d4d4259b3
        //
        $roomName = hash('sha256', site_url(sprintf('chat/video/%06d', $chatVideo->id)));

        //
        $title = __d('web_chat', 'Video Chat : {0}', $authUser->present()->name());

        return $this->getView()
            ->shares('title', $title)
            ->with('url', $url)
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
            $status = __d('web_chat', 'The User with ID: {0} was not found.', $userId);

            return Redirect::to('admin/dashboard')->withStatus($status, 'danger');
        }

        //
        $room = ChatHelper::getChatRoomByUsers($authUser->id, $chatUser->id);

        if ($room === false) {
            $createdRoom = ChatHelper::createRoom($authUser->id, $chatUser->id);

            if ($createdRoom === false) {
                $status = __d('web_chat', 'Chatroom could not be created');

                return Redirect::to('admin/dashboard')->withStatus($status, 'danger');
            }

            $room = ChatHelper::getChatRoomByUsers($authUser->id, $chatUser->id);
        }

        return Redirect::to('admin/chat/video/' .$room->id);
    }

}
