<?php

namespace App\Modules\WebChat\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Assets;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;

use App\Core\BackendController;

use App\Modules\System\Exceptions\ValidationException;
use App\Modules\VideoChat\Helpers\VideoChat as ChatHelper;
use App\Modules\VideoChat\Models\ChatVideo;
use App\Modules\Users\Models\User;


class Chat extends BackendController
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

        // Retrieve the Signaling Server from configuration.
        $url = Config::get('videoChat.url', 'https://sandbox.simplewebrtc.com:443/');

        // Calculate the Chat Room name, using a SHA256 string, resulting something like:
        // f07b631f7a601cd8cbd3332d54f43142c7088a83299f859356f08d1d4d4259b3
        //
        $roomName = hash('sha256', site_url('chat'));

        // Additional assets required on the current View.
        $css = Assets::fetch('css', array(
            'https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/assets/css/emojione.min.css',
            resource_url('css/style.css', 'VideoChat'),
        ));

        $js = Assets::fetch('js', array(
            'https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/lib/js/emojione.min.js',
            resource_url('js/simplewebrtc-latest.js', 'VideoChat'),
            vendor_url('plugins/slimScroll/jquery.slimscroll.min.js', 'almasaeed2010/adminlte'),
        ));

        //
        $title = __d('web_chat', 'Chat : {0}', $authUser->present()->name());

        return $this->getView()
            ->shares('title', $title)
            ->shares('css', $css)
            ->shares('js', $js)
            ->with('url', $url)
            ->with('roomName', $roomName)
            ->with('authUser', $authUser);
    }

}
