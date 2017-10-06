<?php
/**
 * BackendController - A backend Controller for the included example Modules.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\System\Controllers;

use Nova\Database\ORM\Builder as ModelBuilder;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Gate;
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Request;
use Nova\Support\Facades\View;
use Nova\Support\Arr;
use Nova\Support\Str;

use App\Controllers\BaseController as Controller;


abstract class BaseController extends Controller
{
    /**
     * The currently used Theme.
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
    protected function initialize()
    {
        parent::initialize();

        if (! is_null($user = Auth::user())) {
            $menuItems = $this->getMenuItems('backend.menu', $user);
        } else {
            $menuItems = array();
        }

        View::share('menuItems', $menuItems);
    }

    /**
     * Get the menu items for the specified Event and User.
     *
     * @param  string  $event
     * @param  mixed  $user
     * @return array
     */
    protected function getMenuItems($event, $user)
    {
        $gate = Gate::forUser($user);

        // The current URL.
        $url = Request::url();

        // The item path which coresponds with the current URL.
        $path = '';

        // Fire the Event and retrieve the results.
        $results = Event::fire($event, array($user));

        //
        $items = array();

        foreach ($results as $result) {
            if (! is_array($result)) {
                continue;
            }

            foreach ($result as $item) {
                $key = str_replace('.', '.children.', $item['path']);

                if (Arr::has($items, $key)) {
                    continue;
                }

                // Ensure the children array existence.
                else if (! isset($item['children'])) {
                    $item['children'] = array();
                }

                if (! $this->itemIsAllowed($item, $gate, $user)) {
                    continue;
                }

                Arr::set($items, $key, $item);

                if (($item['url'] == $url) && empty($path)) {
                    $path = $item['path'];
                }
            }
        }

        return $this->prepareItems($items, $path, $url);
    }

    protected function itemIsAllowed(array $item, $gate, $user)
    {
        // Check the roles.
        if (isset($item['role']) && ($item['role'] !== 'any')) {
            $roles = explode(',', $item['role']);

            if (! $user->hasRole($roles)) {
                return false;
            }
        }

        // Check the abilities.
        if (isset($item['can'])) {
            list ($ability, $parameters) = $this->parseItemAbility($item);

            if (call_user_func(array($gate, 'denies'), $ability, $parameters)) {
                return false;;
            }
        }

        return true;
    }

    /**
     * Parse an menu item ability.
     *
     * @param  array $item
     * @return array
     */
    protected function parseItemAbility(array $item)
    {
        list($ability, $parameters) = array_pad(explode(':', $item['can'], 2), 2, array());

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return array($ability, $parameters);
    }

    /**
     * Prepare the given menu items.
     *
     * @param  array  $items
     * @param  string $path
     * @param  string $url
     * @return array
     */
    protected function prepareItems(array $items, $path, $url)
    {
        foreach ($items as &$item) {
            $active = false;

            if (($item['url'] == $url) || Str::startsWith($path, $item['path'])) {
                $active = true;
            }

            $item['active'] = $active;

            if (! empty($children = $item['children'])) {
                $item['children'] = $this->prepareItems($children, $path, $url);
            }
        }

        // Sort the menu items by their weight and title.
        usort($items, function ($a, $b)
        {
            if ($a['weight'] === $b['weight']) {
                return strcmp($a['title'], $b['title']);
            }

            return ($a['weight'] < $b['weight']) ? -1 : 1;
        });

        return $items;
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
        $columns = Arr::get($input, 'columns', array());

        // Compute the total count.
        $recordsTotal = $query->count();

        // Compute the draw.
        $draw = intval(Arr::get($input, 'draw', 0));

        // Handle the global searching.
        $search = trim(Arr::get($input, 'search.value'));

        if (! empty($search)) {
            $query->whereNested(function($query) use($columns, $options, $search)
            {
                foreach($columns as $column) {
                    $data = $column['data'];

                    $option = Arr::first($options, function ($key, $value) use ($data)
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

            $option = Arr::first($options, function ($key, $value) use ($data)
            {
                return ($value['data'] == $data);
            });

            $search = trim(Arr::get($column, 'search.value'));

            if (($column['searchable'] == 'true') && (strlen($search) > 0)) {
                $query->where($option['field'], 'LIKE', '%' .$search .'%');
            }
        }

        // Compute the filtered count.
        $recordsFiltered = $query->count();

        // Handle the column ordering.
        $orders = Arr::get($input, 'order', array());

        foreach ($orders as $order) {
            $index = intval($order['column']);

            $column = Arr::get($input, 'columns.' .$index, array());

            //
            $data = $column['data'];

            $option = Arr::first($options, function ($key, $value) use ($data)
            {
                return ($value['data'] == $data);
            });

            if ($column['orderable'] == 'true') {
                $dir = ($order['dir'] === 'asc') ? 'ASC' : 'DESC';

                $field = $option['field'];

                if ($query instanceof ModelBuilder) {
                    $model = $query->getModel();

                    $field = $model->getTable() .'.' .$field;
                }

                $query->orderBy($field, $dir);
            }
        }

        // Handle the pagination.
        $start  = Arr::get($input, 'start',  0);
        $length = Arr::get($input, 'length', 25);

        $query->skip($start)->take($length);

        // Retrieve the data from database.
        $results = $query->get();

        //
        // Format the data on respect of DataTables specs.

        $columns = array();

        foreach ($options as $option) {
            $key = $option['data'];

            //
            $field = Arr::get($option, 'field');

            $columns[$key] = Arr::get($option, 'uses', $field);
        }

        //
        $data = array();

        foreach ($results as $result) {
            $record = array();

            foreach ($columns as $key => $value) {
                // Process for standard columns.
                if (is_string($value)) {
                    $record[$key] = $result->{$value};

                    continue;
                }

                // Process for dynamic columns.
                $record[$key] = call_user_func($value, $result, $key);
            }

            $data[] = $record;
        }

        return array(
            "draw"            => $draw,
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data"            => $data
        );
    }
}
