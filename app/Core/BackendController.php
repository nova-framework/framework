<?php
/**
 * BackendController - A backend Controller for the included example Modules.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Core;

use Nova\Http\Request;
use Nova\Routing\Route;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\View;

use App\Core\ThemedController;

use Modules\Messages\Models\Message;
use Modules\System\Models\Notification;


abstract class BackendController extends ThemedController
{
    /**
     * The currently used Template.
     *
     * @var string
     */
    protected $theme = 'AdminLite';

    /**
     * The currently used Layout.
     *
     * @var mixed
     */
    protected $layout = 'Backend';


    /**
     * Method executed before any action.
     */
    protected function before()
    {
        if (! Auth::check()) {
            // The User is not authenticated; nothing to do.
            return;
        }

        // The User is logged in; setup the Backend Menu.
        $user = Auth::user();

        //
        $items = $this->getMenuItems($user);

        View::share('menuItems', $items);

        //
        $notifications = Notification::where('user_id', $user->id)->unread()->count();

        View::share('notificationCount', $notifications);

        //
        $messages = Message::where('receiver_id', $user->id)->unread()->count();

        View::share('privateMessageCount', $messages);
    }

    private function getMenuItems($user)
    {
        $items = array();

        // Prepare the Event payload.
        $payload = array($user);

        // Fire the Event 'backend.menu' and store the results.
        $results = Event::fire('backend.menu', $payload);

        // Merge all results on a menu items array.
        foreach ($results as $result) {
            if (is_array($result) && ! empty($result)) {
                $items = array_merge($items, $result);
            }
        }

        // Sort the base menu items by their weight and title.
        $items = array_sort($items, function($value) {
            return sprintf('%06d - %s', $value['weight'], $value['title']);
        });

        // Sort the child menu items by their weight and title.
        foreach ($items as &$item) {
            $children = array_get($item, 'children', array());

            if (empty($children)) continue;

            $children = array_sort($children, function($value) {
                return sprintf('%06d - %s', $value['weight'], $value['title']);
            });

            $item['children'] = $children;
        }

        return $items;
    }

    /**
     * Server Side Processor for DataTables.
     *
     * @param Nova\Database\Query\Builder|Nova\Database\ORM\Builder $query
     * @param array $columns
     *
     * @return \Nova\Http\JsonResponse
     */
    protected function dataTable($query, array $columns)
    {
        $totalCount = $query->count();

        // Retrieve the request variables.
        $input = Input::only('columns', 'draw', 'start', 'length', 'search', 'order');

        $requestColumns = array_get($input, 'columns', array());

        $draw   = array_get($input, 'draw',   0);
        $start  = array_get($input, 'start',  0);
        $length = array_get($input, 'length', 25);
        $order  = array_get($input, 'order',  array());

        // Handle the global searching.
        $search = trim(array_get($input, 'search.value', ''));

        if (! empty($search)) {
            $query->whereNested(function($query) use($requestColumns, $columns, $search)
            {
                foreach($requestColumns as $requestColumn) {
                    $data = $requestColumn['data'];

                    $column = array_first($columns, function ($key, $value) use ($data)
                    {
                        return ($value['dt'] == $data);
                    });

                    if ($requestColumn['searchable'] == 'true') {
                        $field = $column['db'];

                        $query->orWhere($field, 'LIKE', '%' .$search .'%');
                    }
                }
            });
        }

        // Handle the column searching.
        foreach($requestColumns as $requestColumn) {
            $data = $requestColumn['data'];

            $column = array_first($columns, function ($key, $value) use ($data)
            {
                return ($value['dt'] == $data);
            });

            $search = trim(array_get($requestColumn, 'search.value', ''));

            if (($requestColumn['searchable'] == 'true') && (strlen($search) > 0)) {
                $field = $column['db'];

                $query->where($field, 'LIKE', '%' .$search .'%');
            }
        }

        $filteredCount = $query->count();

        // Handle the column ordering.
        if (! empty($order)) {
            foreach ($order as $options) {
                $columnIdx = intval($options['column']);

                $requestColumn = array_get($input, 'columns.' .$columnIdx, array());

                //
                $data = $requestColumn['data'];

                $column = array_first($columns, function ($key, $value) use ($data)
                {
                    return ($value['dt'] == $data);
                });

                if ($requestColumn['orderable'] == 'true') {
                    $field = $column['db'];

                    $dir = ($options['dir'] === 'asc') ? 'ASC' : 'DESC';

                    $query->orderBy($field, $dir);
                }
            }
        }

        // Handle the pagination and retrieve the data from database.
        $results = $query->skip($start)->take($length)->get();

        // Format the retrieved data to respect the DataTables specs.
        $data = array();

        foreach ($results as $result) {
            $record = array();

            foreach ($columns as $column) {
                $key = $column['dt'];

                $formatter = array_get($column, 'formatter');

                $field = array_get($column, 'db');

                if (! is_null($field)) {
                    $value = $result->{$field};

                    if (! is_null($formatter)) {
                        $value = call_user_func($formatter, $result, $value);
                    }
                }

                // Handle the dynamic fields.
                else if (is_null($formatter)) {
                    throw new \Exception("Formatter not defined for column [$key]");
                } else {
                    $value = call_user_func($formatter, $result);
                }

                $record[$key] = $value;
            }

            $data[] = $record;
        }

        return Response::json(array(
            "draw"            => intval($draw),
            "recordsTotal"    => $totalCount,
            "recordsFiltered" => $filteredCount,
            "data"            => $data
        ));
    }

}
