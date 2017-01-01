<?php
/**
 * Dasboard - Implements a simple Administration Dashboard.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\System\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\View;

use App\Core\BackendController;

use App\Modules\System\Models\Log as Logger;
use App\Modules\Users\Models\User;


class Logs extends BackendController
{

    public function index()
    {
        $items = Logger::with('group')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Convert the information.
        $logs = array();

        foreach ($items->getItems() as $item) {
            try {
                $user = User::findOrFail($item->user_id);

                //
                $username = $user->username;
            }
            catch (ModelNotFoundException $e) {
                $username = $item->user_id;
            }

            array_push($logs, array(
                'id'       => $item->getkey(),
                'date'     => $item->created_at->formatLocalized(__d('system', '%d %b %Y, %H:%M:%S')),
                'username' => $username,
                'group'    => $item->group->name,
                'message'  => $item->message ?: '-',
            ));
        }

        return $this->getView()
            ->shares('title', __d('logs', 'Logs'))
            ->withLinks($items->links())
            ->withLogs($logs);
    }

    public function clear()
    {
        Logger::truncate();

        // Prepare the flash message.
        $status = __d('system', 'The Logs was successfully cleared.');

        return Redirect::to('admin/logs')->withStatus($status);
    }

}
