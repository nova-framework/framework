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
use Nova\Support\Facades\Redirect;
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
        $columns = $this->getMenuItems($user);

        View::share('menuItems', $columns);

        //
        $notifications = Notification::where('user_id', $user->id)->unread()->count();

        View::share('notificationCount', $notifications);

        //
        $messages = Message::where('receiver_id', $user->id)->unread()->count();

        View::share('privateMessageCount', $messages);
    }

    private function getMenuItems($user)
    {
        $columns = array();

        // Prepare the Event payload.
        $payload = array($user);

        // Fire the Event 'backend.menu' and store the results.
        $results = Event::fire('backend.menu', $payload);

        // Merge all results on a menu items array.
        foreach ($results as $result) {
            if (is_array($result) && ! empty($result)) {
                $columns = array_merge($columns, $result);
            }
        }

        // Sort the base menu items by their weight and title.
        $columns = array_sort($columns, function($value) {
            return sprintf('%06d - %s', $value['weight'], $value['title']);
        });

        // Sort the child menu items by their weight and title.
        foreach ($columns as &$column) {
            $children = array_get($column, 'children', array());

            if (empty($children)) continue;

            $children = array_sort($children, function($value) {
                return sprintf('%06d - %s', $value['weight'], $value['title']);
            });

            $column['children'] = $children;
        }

        return $columns;
    }

    /**
     * Server Side Processor for DataTables.
     *
     * @param Nova\Database\Query\Builder|Nova\Database\ORM\Builder $query
     * @param array $input
     * @param array $options
     *
     * @return array
     */
    protected function dataTable($query, array $input, array $options)
    {
        $columns = array_get($input, 'columns', array());

        // Compute the total count.
        $totalCount = $query->count();

        // Compute the draw.
        $draw = intval(array_get($input, 'draw', 0));

        // Handle the global searching.
        $search = trim(array_get($input, 'search.value'));

        if (! empty($search)) {
            $query->whereNested(function($query) use($columns, $options, $search)
            {
                foreach($columns as $column) {
                    $data = $column['data'];

                    $option = array_first($options, function ($key, $value) use ($data)
                    {
                        return ($value['data'] == $data);
                    });

                    if ($column['searchable'] == 'true') {
                        $query->orWhere($option['field'], 'LIKE', '%' .$search .'%');
                    }
                }
            });
        }

        // Handle the column searching.
        foreach($columns as $column) {
            $data = $column['data'];

            $option = array_first($options, function ($key, $value) use ($data)
            {
                return ($value['data'] == $data);
            });

            $search = trim(array_get($column, 'search.value'));

            if (($column['searchable'] == 'true') && (strlen($search) > 0)) {
                $query->where($option['field'], 'LIKE', '%' .$search .'%');
            }
        }

        // Compute the filtered count.
        $filteredCount = $query->count();

        // Handle the column ordering.
        $orders = array_get($input, 'order', array());

        foreach ($orders as $order) {
            $index = intval($order['column']);

            $column = array_get($input, 'columns.' .$index, array());

            //
            $data = $column['data'];

            $option = array_first($options, function ($key, $value) use ($data)
            {
                return ($value['data'] == $data);
            });

            if ($column['orderable'] == 'true') {
                $dir = ($order['dir'] === 'asc') ? 'ASC' : 'DESC';

                $query->orderBy($option['field'], $dir);
            }
        }

        // Handle the pagination.
        $start  = array_get($input, 'start',  0);
        $length = array_get($input, 'length', 25);

        $query->skip($start)->take($length);

        // Retrieve the data from database.
        $results = $query->get();

        // Format the data on respect of DataTables specs.
        $data = array();

        foreach ($results as $result) {
            $record = array();

            foreach ($options as $option) {
                $key = $option['data'];

                 // Process for dynamic columns.
                if (! is_null($callable = array_get($option, 'uses'))) {
                    $record[$key] = call_user_func($callable, $result, $key);

                    continue;
                }

                // Process for standard columns.
                $record[$key] = $result->{$option['field']};
            }

            $data[] = $record;
        }

        return array(
            "draw"            => $draw,
            "recordsTotal"    => $totalCount,
            "recordsFiltered" => $filteredCount,
            "data"            => $data
        );
    }

}
