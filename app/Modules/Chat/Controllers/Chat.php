<?php

namespace App\Modules\Chat\Controllers;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\DB;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Response;

use App\Modules\Platform\Controllers\BaseController;
use App\Models\User;

use App\Modules\Chat\Models\Message;

use Carbon\Carbon;


class Chat extends BaseController
{

    public function index()
    {
        $authUser = Auth::user();

        return $this->createView()
            ->shares('title', __d('chat', 'Chat'))
            ->with('authUser', $authUser);
    }
}
