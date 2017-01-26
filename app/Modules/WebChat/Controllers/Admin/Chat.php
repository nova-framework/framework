<?php

namespace App\Modules\WebChat\Controllers\Admin;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;

use App\Modules\System\Exceptions\ValidationException;
use App\Modules\Users\Models\User;
use App\Modules\WebChat\Core\Controller;


class Chat extends Controller
{

    public function index()
    {
        $authUser = Auth::user();

        // Retrieve the Signaling Server from configuration.
        $url = Config::get('videoChat.url', 'https://sandbox.simplewebrtc.com:443/');

        // Calculate the Chat Room name, using a SHA256 string, resulting something like:
        // f07b631f7a601cd8cbd3332d54f43142c7088a83299f859356f08d1d4d4259b3
        //
        $roomName = hash('sha256', site_url('chat'));

        //
        $title = __d('web_chat', 'Chat : {0}', $authUser->present()->name());

        return $this->getView()
            ->shares('title', $title)
            ->with('url', $url)
            ->with('roomName', $roomName)
            ->with('authUser', $authUser);
    }

}
