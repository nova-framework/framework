<?php

namespace App\Modules\System\Helpers;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Auth;

use App\Modules\System\Models\Log;
use App\Modules\System\Models\LogGroup;


class Logger
{
    public static function create($message, $slug = 'generic', $url = null)
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

        // Create the Log entry.
        Log::create(array(
            'user_id'  => $user->getKey(),
            'group_id' => $group->getKey(),
            'message'  => $message,
            'url'      => $url,
        ));
    }
}
