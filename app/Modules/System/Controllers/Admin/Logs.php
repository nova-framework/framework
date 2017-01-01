<?php
/**
 * Dasboard - Implements a simple Administration Dashboard.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\System\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\View;

use App\Core\BackendController;

use App\Modules\System\Models\UserLogs;
use App\Modules\Users\Models\User;


class Logs extends BackendController
{

    public function index()
    {
        $items = UserLogs::orderBy('created_at', 'desc')->paginate(50);

        // Convert the information.
        $logs = array();

        foreach ($items->getItems() as $item) {
            try {
                $user = User::findOrFail($item->user_id);

                //
                $username = $user->username;
                $email = $user->email;
            }
            catch (ModelNotFoundException $e) {
                $username = $item->user_id;

                $email = '-';
            }

            switch ($item->action) {
                case 'saved':
                    $action = __d('system', 'Saved');

                    break;
                case 'updated':
                    $action = __d('system', 'Updated');

                    break;
                case 'removed':
                    $action = __d('system', 'Removed');

                    break;
            }

            array_push($logs, array(
                'username'   => $username,
                'email'      => $email,
                'action'     => $action,
                'model'      => $item->action_model,
                'date' => $item->created_at->formatLocalized(__d('system', '%d %b %Y, %H:%M'))
            ));
        }

        return $this->getView()
            ->shares('title', __d('logs', 'Logs'))
            ->withLinks($items->links())
            ->withLogs($logs);
    }

}
