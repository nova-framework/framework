<?php

namespace App\Modules\Logs\Helpers;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Request;

use App\Modules\Logs\Models\Log;
use App\Modules\Logs\Models\LogGroup;


class Logger
{
    public static function create($message, $slug = 'generic', $url = 'current')
    {
        // Retrieve the current User instance.
        $user = Auth::user();

        // Retrieve the Logging Group with fallback to generic one.
        try {
            $group = LogGroup::where('slug', $slug)
                ->remember(1440)
                ->firstOrFail();
        }
        catch (ModelNotFoundException $e) {
            $group = LogGroup::where('slug', 'generic')
                ->remember(1440)
                ->first();
        }

        if ($url == 'referrer') {
            $url = Request::header('referer');
        } else if ($url == 'current') {
            $url = Request::fullUrl();
        }

        // Create the Log entry.
        Log::create(array(
            'user_id'  => $user->getKey(),
            'group_id' => $group->getKey(),
            'message'  => $message,
            'url'      => $url,
        ));
    }
}
